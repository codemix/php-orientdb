<?php

namespace Orienta\Common;

class Math
{
    /**
     * Convert twos-complement integer after unpack() on x64 systems.
     *
     * @param int $int The integer to convert.
     * @return int The converted integer.
     */
    public static function convertComplementInt($int)
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
    public static function convertComplementShort($short)
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
    public static function unpackI64($hi, $low)
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
