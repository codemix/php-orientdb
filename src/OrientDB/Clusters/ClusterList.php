<?php

namespace OrientDB\Clusters;

use OrientDB\Common\MapInterface;
use OrientDB\Common\MapTrait;
use OrientDB\Databases\Database;
use OrientDB\Exceptions\Exception;

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
     * Create a cluster with the given name.
     *
     * @param string $name The name of the cluster.
     * @param string $location The cluster location, e.g. 'PHYSICAL' or 'MEMORY'.
     * @param array $options The extra options for the cluster.
     *
     * @return Cluster The newly created cluster.
     */
    public function create($name, $location = 'PHYSICAL', array $options = [])
    {
        $name = strtolower($name);
        $options['name'] = $name;
        $options['location'] = $location;
        $id = $this->database->execute('dataclusterAdd', $options);
        $cluster = new Cluster($this->database, [
            'id' => $id,
            'name' => $name,
            'location' => $location
        ]);
        $this->items[$name] = $cluster;
        return $cluster;
    }

    /**
     * Determine whether a cluster with the given name exists.
     *
     * @param string $name The name of the cluster to check.
     *
     * @return bool true if the cluster exists.
     */
    public function exists($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * Delete a cluster with the given id.
     *
     * @param Cluster|int $id The cluster instance or id.
     *
     * @return Cluster The deleted cluster instance.
     */
    public function drop($id) {
        if ($id instanceof Cluster) {
            $id = $id->id;
        }
        $this->database->execute('dataclusterDrop', [
            'id' => $id
        ]);
        foreach($this->items as $key => $value) {
            if ($value->id == $id) {
                unset($this->items[$key]);
                return $value;
            }
        }
        return null;
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
