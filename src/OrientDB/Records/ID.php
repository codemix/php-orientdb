<?php

namespace OrientDB\Records;

class ID implements \JsonSerializable
{
    /**
     * @var int The cluster the record belongs to.
     */
    public $cluster;

    /**
     * @var int The position of the record in the cluster.
     */
    public $position;


    /**
     * # Record ID Constructor.
     *
     * @param int|string|array $cluster The cluster id, string representation or configuration object
     * @param int $position The position in the cluster, if $cluster is an integer.
     */
    public function __construct ($cluster = -1, $position = -1)
    {
        if (is_array($cluster)) {
            if (isset($cluster['cluster'])) {
                $this->cluster = $cluster['cluster'];
            }
            if (isset($cluster['position'])) {
                $this->position = $cluster['position'];
            }
        }
        else if (is_string($cluster) && $cluster[0] === '#') {
            list($this->cluster, $this->position) = self::parseString($cluster);
        }
        else {
            $this->cluster = (int) $cluster;
            $this->position = (int) $position;
        }
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        return $this->__toString();
    }


    /**
     * @return string A string representation of the record id, e.g. "#12:10".
     */
    public function __toString()
    {
        return '#'.$this->cluster.':'.$this->position;
    }


    public static function parseString($input)
    {
        return explode(':', substr($input, 1));
    }
}
