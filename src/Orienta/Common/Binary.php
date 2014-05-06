<?php

namespace Orienta\Common;

class Binary
{

    /**
     * Pack a byte.
     *
     * @param int $value
     *
     * @return string the packed byte.
     */
    public static function packByte($value)
    {
        return chr($value);
    }

    /**
     * Unpack a byte.
     *
     * @param mixed $value
     *
     * @return int the byte unpacked
     */
    public static function unpackByte($value)
    {
        return ord($value);
    }

    /**
     * Pack a short.
     *
     * @param int $value
     *
     * @return string the packed short
     */
    public static function packShort($value)
    {
        return pack('n', $value);
    }

    /**
     * Unpack a short.
     *
     * @param mixed $value
     *
     * @return int the short unpacked
     */
    public static function unpackShort($value)
    {
        return Math::convertComplementShort(unpack('n', $value)[1]);
    }

    /**
     * Pack an integer.
     *
     * @param int $value
     *
     * @return string the packed integer
     */
    public static function packInt($value)
    {
        return pack('N', $value);
    }

    /**
     * Unpack an integer.
     *
     * @param mixed $value the value to unpack
     *
     * @return int the integer unpacked
     */
    public static function unpackInt($value)
    {
        $value = unpack('N', $value);
        return Math::convertComplementInt(reset($value));
    }


    /**
     * Pack a long.
     *
     * @todo 64 bit not yet supported!
     *
     * @param int $value
     *
     * @return string the packed long
     */
    public static function packLong($value)
    {
        return str_repeat(chr(0), 4).pack('N', $value);
    }

    /**
     * Unpack a long.
     *
     * @param mixed $value
     *
     * @return int the long unpacked
     */
    public static function unpackLong($value)
    {
        $first = substr($value, 0, 4);
        $last = substr($value, 4, 4);
        // First of all, unpack 8 bytes, divided into hi and low parts
        $hi = unpack('N', $first);
        $hi = reset($hi);
        $low = unpack('N', $last);
        $low = reset($low);
        // Unpack 64-bit signed long
        return Math::unpackI64($hi, $low);
    }

    /**
     * Pack a string.
     *
     * @param string $value
     *
     * @return string the packed string.
     */
    public static function packString($value)
    {
        return self::packInt(strlen($value)).$value;
    }

    /**
     * Unpack a string.
     *
     * @param mixed $value
     *
     * @return string|null the string unpack, or null if it's empty.
     */
    public static function unpackString($value)
    {
        $length = self::unpackInt(substr($value, 0, 4));
        $value = substr($value, 4, $length);
        if ($length === -1) {
            return null;
        }
        else if ($length === 0) {
            return '';
        }
        else {
            return $value;
        }
    }

    /**
     * Pack bytes.
     *
     * @param string $value
     *
     * @return string the packed string.
     */
    public static function packBytes($value)
    {
        return self::packInt(strlen($value)).$value;
    }

    /**
     * Unpack bytes.
     *
     * @param mixed $value
     *
     * @return string|null the string unpack, or null if it's empty.
     */
    public static function unpackBytes($value)
    {
        $length = self::unpackInt(substr($value, 0, 4));
        $value = substr($value, 4, $length);
        if ($length === -1) {
            return null;
        }
        else if ($length === 0) {
            return '';
        }
        else {
            return $value;
        }
    }


}
