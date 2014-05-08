<?php

namespace Orienta\Clusters;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Databases\Database;

class Cluster implements ConfigurableInterface, \Countable
{
    use ConfigurableTrait;

    /**
     * @var string The name of the cluster
     */
    public $name;

    /**
     * @var int The id of the cluster.
     */
    public $id;

    /**
     * @var string The cluster type.
     */
    public $type;

    /**
     * @var int The data segment for the cluster.
     */
    public $dataSegment;

    /**
     * @var Database The database the cluster belongs to.
     */
    protected $database;

    /**
     * # Constructor
     *
     * @param Database $database The database the cluster belongs to.
     * @param array $config The configuration for the cluster.
     */
    public function __construct(Database $database, array $config = [])
    {
        $this->database = $database;
        $this->configure($config);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        return $this->database->execute('dataclusterCount', [
            'ids' => [$this->id]
        ]);
    }


    public function load($position, $fetchPlan = '')
    {
        $result = $this->database->execute('recordLoad', [
            'cluster' => $this->id,
            'position' => $position,
            'fetchPlan' => ''
        ]);
        return $result;
    }

}
