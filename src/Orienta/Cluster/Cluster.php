<?php

namespace Orienta\Cluster;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Database\Database;

class Cluster implements ConfigurableInterface
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

}
