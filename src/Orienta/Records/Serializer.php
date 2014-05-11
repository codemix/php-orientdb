<?php

namespace Orienta\Records;

class Serializer
{
    /**
     * Serialize a value.
     *
     * @param mixed $value The value to serialize.
     *
     * @param bool $embedded Whether this is a value embedded in another.
     *
     * @return string The serialized value.
     */
    public static function serialize($value, $embedded = false)
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
            return self::serializeDocument($value, $embedded);
        }
        else if ($value instanceof \DateTime) {
            return $value->getTimestamp().'t';
        }
        else {
            return '';
        }
    }

    protected static function serializeDocument(SerializableInterface $document, $embedded = false)
    {
        $array = $document->recordSerialize();
        $segments = [];
        foreach($array as $key => $value)
        {
            if (substr($key, 0, 1) === '@') {
                continue;
            }
            $segments[] = $key.':'.self::serialize($value, true);
        }

        $assembled = implode(',',$segments);
        if (isset($array['@class'])) {
            $assembled = $array['@class'].'@'.$assembled;
        }
        if ($embedded) {
            return '('.$assembled.')';
        }
        else {
            return $assembled;
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
            $values[] = self::serialize($value, true);
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
