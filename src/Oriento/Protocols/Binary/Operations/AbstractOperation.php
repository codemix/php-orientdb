<?php

namespace Oriento\Protocols\Binary\Operations;

use Oriento\Common\Binary;
use Oriento\Common\ConfigurableInterface;
use Oriento\Common\ConfigurableTrait;
use Oriento\Common\Math;
use Oriento\Exceptions\Exception;
use Oriento\Protocols\Binary\Socket;
use Oriento\Record\Deserializer;

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
    public $sessionId = -1;


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
            $this->readByte(); // discard the first byte of the error
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
        if (!$this->socket->negotiated) {
            $protocol = $this->readShort();
            $this->socket->negotiated = true;
        }
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
        $this->socket->write(Binary::packByte($value));
    }

    /**
     * Read a byte from the socket.
     *
     * @return int the byte read
     */
    protected function readByte()
    {
        $value = $this->socket->read(1);
        return Binary::unpackByte($value);
    }

    /**
     * Write a boolean to the socket.
     *
     * @param bool $value
     */
    protected function writeBoolean($value)
    {
        $this->socket->write(Binary::packByte((bool) $value));
    }

    /**
     * Read a boolean from the socket.
     *
     * @return bool the boolean read
     */
    protected function readBoolean()
    {
        $value = $this->socket->read(1);
        return (bool) Binary::unpackByte($value);
    }

    /**
     * Write a short to the socket.
     *
     * @param int $value
     */
    protected function writeShort($value)
    {
        $this->socket->write(Binary::packShort($value));
    }

    /**
     * Read a short from the socket.
     *
     * @return int the short read
     */
    protected function readShort()
    {
        return Binary::unpackShort($this->socket->read(2));
    }

    /**
     * Write an integer to the socket.
     *
     * @param int $value
     */
    protected function writeInt($value)
    {
        $this->socket->write(Binary::packInt($value));
    }

    /**
     * Read an integer from the socket.
     *
     * @return int the integer read
     */
    protected function readInt()
    {
        return Binary::unpackInt($this->socket->read(4));
    }


    /**
     * Write a long to the socket.
     *
     * @param int $value
     */
    protected function writeLong($value)
    {
        $this->socket->write(Binary::packLong($value));
    }

    /**
     * Read a long from the socket.
     *
     * @return int the integer read
     */
    protected function readLong()
    {
        return Binary::unpackLong($this->socket->read(8));
    }

    /**
     * Write a string to the socket.
     *
     * @param string $value
     */
    protected function writeString($value)
    {
        $this->socket->write(Binary::packString($value));
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
        $this->socket->write(Binary::packBytes($value));
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
        if ($hasMore === 1) {
            $next = $this->readError();
        }
        else {
            $javaStackTrace = $this->readBytes();
        }

        return new Exception($type.': '.$message);
    }

    /**
     * Read a serialized object from the remote server.
     *
     * @return mixed
     */
    protected function readSerialized()
    {
        $serialized = $this->readString();
        return Deserializer::deserialize($serialized);
    }


}
