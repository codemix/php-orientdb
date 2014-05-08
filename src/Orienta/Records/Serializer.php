<?php

namespace Orienta\Records;

class Serializer
{
    /**
     * Serialize a value.
     *
     * @param mixed $value The value to serialize.
     *
     * @return string The serialized value.
     */
    public static function serialize($value)
    {
        if ($value === null) {
            return 'null';
        }
        if (is_string($value)) {
            return '"'.str_replace('"', '\\"', str_replace('\\', '\\\\', $value)).'"';
        }
        else if (is_float($value)) {
            return $value.'f';
        }
        else if (is_int($value)) {
            return $value;
        }
        else if (is_bool($value)) {
            return $value ? 'true' : 'false';
        }
        else if (is_array($value)) {
            return self::serializeArray($value);
        }
        else if ($value instanceof SerializableInterface) {
            return self::serialize($value->recordSerialize());
        }
        else if ($value instanceof \DateTime) {
            return $value->getTimestamp().'t';
        }
        else {
            return '';
        }
    }

    /**
     * Serialize an array of values.
     * If the array is associative a `map` will be returned, otherwise a plain array.
     *
     * @param array $array the array to serialize
     *
     * @return string the serialized array or map.
     */
    protected static function serializeArray(array $array)
    {
        $isMap = false;
        $keys = [];
        $values = [];

        foreach($array as $key => $value) {
            if (!$isMap && is_string($key) && strlen($key)) {
                $isMap = true;
            }
            if ($isMap) {
                $keys[] = '"'.str_replace('"', '\\"', str_replace('\\', '\\\\', $key)).'"';
            }
            else {
                $keys[] = '"'.$key.'"';
            }
            $values[] = self::serialize($value);
        }
        if ($isMap) {
            $parts = [];
            foreach($keys as $i => $key) {
                $parts[] = $key.':'.$values[$i];
            }
            return '{'.implode(',',$parts).'}';
        }
        else {
            return '['.implode(',',$values).']';
        }
    }
}
