<?php

namespace Oriento\Protocols\Binary\Operations;

use Oriento\Common\ConfigurableInterface;
use Oriento\Common\ConfigurableTrait;
use Oriento\Exceptions\Exception;
use Oriento\Protocols\Binary\Socket;

abstract class AbstractOperation implements ConfigurableInterface
{
    use ConfigurableTrait;

    /**
     * @var int The op code.
     */
    public $opCode;

    /**
     * @var int The session id, if any.
     */
    public $sessionId;


    /**
     * @var Socket The socket to write to.
     */
    public $socket;

    /**
     * Write the data to the socket.
     */
    abstract protected function write();

    /**
     * Read the response from the socket.
     *
     * @return mixed the response.
     */
    abstract protected function read();

    /**
     * Write the request header.
     */
    protected function writeHeader()
    {
        $this->writeByte($this->opCode);
        $this->writeInt($this->sessionId);
    }

    /**
     * Read the response header.
     *
     * @throws \Oriento\Exceptions\Exception if the response indicates an error.
     */
    protected function readHeader()
    {
        $status = $this->readByte();
        $sessionId = $this->readInt();
        if ($status === 1) {
            $error = $this->readError();
            throw $error;
        }
    }

    /**
     * Execute the operation.
     *
     * @return mixed The response from the server.
     */
    public function execute()
    {
        $this->writeHeader();
        $this->write();
        $this->readHeader();
        return $this->read();
    }

    /**
     * Write a byte to the socket.
     *
     * @param int $value
     */
    protected function writeByte($value)
    {
        $this->socket->write($value);
    }

    /**
     * Read a byte from the socket.
     *
     * @return int the byte read
     */
    protected function readByte()
    {
        return (int) $this->socket->read(1);
    }

    /**
     * Write a short to the socket.
     *
     * @param int $value
     */
    protected function writeShort($value)
    {
        $this->socket->write(pack('n', $value));
    }

    /**
     * Read a short from the socket.
     *
     * @return int the short read
     */
    protected function readShort()
    {
        return $this->convertComplementShort(unpack('n', $this->socket->read(2))[0]);
    }

    /**
     * Write an integer to the socket.
     *
     * @param int $value
     */
    protected function writeInt($value)
    {
        $this->socket->write(pack('N', $value));
    }

    /**
     * Read an integer from the socket.
     *
     * @return int the integer read
     */
    protected function readInt()
    {
        return $this->convertComplementInt(unpack('N', $this->socket->read(4))[0]);
    }


    /**
     * Write a long to the socket.
     *
     * @param int $value
     */
    protected function writeLong($value)
    {
        $this->socket->write( str_repeat(chr(0), 4).pack('N', $value));
    }

    /**
     * Read a long from the socket.
     *
     * @return int the integer read
     */
    protected function readLong()
    {
        // First of all, read 8 bytes, divided into hi and low parts
        $hi = unpack('N', $this->socket->read(4));
        $hi = reset($hi);
        $low = unpack('N', $this->socket->read(4));
        $low = reset($low);
        // Unpack 64-bit signed long
        return $this->unpackI64($hi, $low);
    }

    /**
     * Write a string to the socket.
     *
     * @param string $value
     */
    protected function writeString($value)
    {
        $this->writeInt(strlen($value));
        $this->socket->write($value);
    }

    /**
     * Read a string from the socket.
     *
     * @return string|null the string read, or null if it's empty.
     */
    protected function readString()
    {
        $length = $this->readInt();
        if ($length === -1) {
            return null;
        }
        else if ($length === 0) {
            return '';
        }
        else {
            return $this->socket->read($length);
        }
    }

    /**
     * Write bytes to the socket.
     *
     * @param string $value
     */
    protected function writeBytes($value)
    {
        $this->writeInt(strlen($value));
        $this->socket->write($value);
    }

    /**
     * Read bytes from the socket.
     *
     * @return string|null the bytes read, or null if it's empty.
     */
    protected function readBytes()
    {
        $length = $this->readInt();
        if ($length === -1) {
            return null;
        }
        else if ($length === 0) {
            return '';
        }
        else {
            return $this->socket->read($length);
        }
    }

    /**
     * Read an error from the remote server and turn it into an exception.
     *
     * @return Exception the wrapped exception object.
     */
    protected function readError()
    {
        $type = $this->readString();
        $message = $this->readString();
        $hasMore = $this->readByte();
        if ($hasMore) {
            $next = $this->readError();
        }
        else {
            $javaStackTrace = $this->readBytes();
        }
        return new Exception($type.': '.$message);
    }

    /**
     * Convert twos-complement integer after unpack() on x64 systems.
     *
     * @param int $int The integer to convert.
     * @return int The converted integer.
     */
    protected function convertComplementInt($int)
    {
        /*
         *  Valid 32-bit signed integer is -2147483648 <= x <= 2147483647
         *  -2^(n-1) < x < 2^(n-1) -1 where n = 32
         */
        if ($int > 2147483647) {
            return -(($int ^ 0xFFFFFFFF) + 1);
        }
        return $int;
    }

    /**
     * Convert twos-complement short after unpack() on x64 systems.
     *
     * @param int $short The short to convert.
     * @return int The converted short.
     */
    protected function convertComplementShort($short)
    {
        /*
         *  Valid 16-bit signed integer is -32768 <= x <= 32767
         *  -2^(n-1) < x < 2^(n-1) -1 where n = 16
         */
        if ($short > 32767) {
            return -(($short ^ 0xFFFF) + 1);
        }
        return $short;
    }

    /**
     * Unpacks 64 bits signed long
     *
     * @param $hi int Hi bytes of long
     * @param $low int Low bytes of long
     * @return int|string
     */
    protected function unpackI64($hi, $low)
    {
        // Packing is:
        // OrientDBHelpers::hexDump(pack('NN', $int >> 32, $int & 0xFFFFFFFF));

        // If x64 system, just shift hi bytes to the left, add low bytes. Piece of cake.
        if (PHP_INT_SIZE === 8) {
            return ($hi << 32) + $low;
        }

        // x32
        // Check if long could fit into int
        $hiComplement = self::convertComplementInt($hi);
        if ($hiComplement === 0) {
            // Hi part is 0, low will fit in x32 int
            return $low;
        } elseif ($hiComplement === -1) {
            // Hi part is negative, so we just can convert low part
            if ($low >= 0x80000000) {
                // Check if low part is lesser than minimum 32 bit signed integer
                return self::convertComplementInt($low);
            }
        }

        // Sign char
        $sign = '';
        $lastBit = 0;
        // This is negative number
        if ($hiComplement < 0) {
            $hi = ~$hi;
            $low = ~$low;
            $lastBit = 1;
            $sign = '-';
        }

        // Format bytes properly
        $hi = sprintf('%u', $hi);
        $low = sprintf('%u', $low);

        // Do math
        $temp = bcmul($hi, '4294967296');
        $temp = bcadd($low, $temp);
        $temp = bcadd($temp, $lastBit);
        return $sign . $temp;
    }
}
