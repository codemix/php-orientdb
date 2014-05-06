<?php

namespace Orienta\Cluster;

use Orienta\Common\MapInterface;
use Orienta\Common\MapTrait;
use Orienta\Database\Database;

class ClusterList implements MapInterface
{
    use MapTrait;

    /**
     * @var Database The database the list of clusters is for.
     */
    protected $database;

    /**
     * @param Database $database The database the list of clusters is for.
     * @param array $items The items in the list
     */
    public function __construct($database, array $items = [])
    {
        $this->database = $database;
        foreach($items as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * @inheritDoc
     * @param mixed $offset
     * @param Cluster $value
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value)) {
            $value = new Cluster($this->database, $value);
        }
        $this->items[$value->name] = $value;
    }


}
