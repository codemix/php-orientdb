<?php

namespace OrientDB\Databases;

use OrientDB\Classes\ClassInterface;
use OrientDB\Classes\ClassList;
use OrientDB\Client;
use OrientDB\Clusters\Cluster;
use OrientDB\Clusters\ClusterList;
use OrientDB\Common\ConfigurableInterface;
use OrientDB\Common\ConfigurableTrait;
use OrientDB\Common\MagicInterface;
use OrientDB\Common\MagicTrait;
use OrientDB\Queries\Query;
use OrientDB\Queries\ResultList;
use OrientDB\Queries\Types\Command;
use OrientDB\Records\DocumentInterface;
use OrientDB\Records\ID;
use OrientDB\Records\RecordInterface;

/**
 * # Database
 *
 * @property ClusterList $clusters
 * @property ClassList $classes
 *
 * @package OrientDB\Databases
 */
class Database implements ConfigurableInterface, MagicInterface
{
    use ConfigurableTrait;
    use MagicTrait;

    /**
     * @var string The name of the database.
     */
    public $name;

    /**
     * @var string The database storage type.
     */
    public $storage = 'plocal';

    /**
     * @var string The database type.
     */
    public $type = 'graph';

    /**
     * @var string The file id for the database.
     */
    public $fileId;

    /**
     * @var string The username for the database.
     */
    public $username = 'admin';

    /**
     * @var string The password for the database.
     */
    public $password = 'admin';

    /**
     * @var Client The client instance this database belongs to.
     */
    protected $client;

    /**
     * @var int The session id.
     */
    protected $sessionId = -1;

    /**
     * @var ClusterList A list of clusters in the database.
     */
    protected $clusters;

    /**
     * @var ClassList A list of classes in the database.
     */
    protected $classes;

    /**
     * @var string[] A map of OrientDB class names to PHP class names
     */
    protected $classHandlers = [];

    /**
     * @param Client $client The client the database belongs to.
     * @param string $name The name of the database
     * @param string|null $locationString The location string for the database.
     */
    public function __construct($client, $name, $locationString = null)
    {
        $this->client = $client;
        $this->name = $name;
        if ($locationString !== null) {
            list($this->storage, $this->fileId) = explode(':', $locationString);
        }
    }

    /**
     * Gets the Clusters
     * @return \OrientDB\Clusters\ClusterList
     */
    public function getClusters()
    {
        if ($this->clusters === null) {
            $this->open();
        }
        return $this->clusters;
    }

    /**
     * Get the cluster with the given name.
     *
     * @param string $name The name of the cluster to get.
     *
     * @return Cluster The cluster instance.
     */
    public function getCluster($name)
    {
        return $this->getClusters()->offsetGet($name);
    }

    /**
     * Sets the Classes
     *
     * @param \OrientDB\Classes\ClassList $classes
     *
     * @return $this the current object
     */
    public function setClasses($classes)
    {
        $this->classes = $classes;
        return $this;
    }

    /**
     * Gets the Classes
     * @return \OrientDB\Classes\ClassList
     */
    public function getClasses()
    {
        if ($this->classes === null) {
            $result = $this->execute('recordLoad', [
                'cluster' => 0,
                'position' => 1
            ]);
            $this->classes = new ClassList($this, $result->classes);
        }
        return $this->classes;
    }

    /**
     * Gets a class with the specific name.
     *
     * @param string $name The name of the class to get.
     *
     * @return null|\OrientDB\Classes\ClassInterface The class instance.
     */
    public function getClass($name)
    {
        return $this->getClasses()->offsetGet($name);
    }

    /**
     * @param string|ID $id The record ID to load.
     * @param array $options The options for the `RecordLoad` command.
     *
     * @return RecordInterface|null The loaded record, if it exists.
     */
    public function loadRecord($id, array $options = [])
    {
        if (!($id instanceof ID)) {
            $id = new ID($id);
        }
        $params = [
            'cluster' => $id->cluster,
            'position' => $id->position
        ];
        return $this->execute('recordLoad', array_merge($params, $options));
    }

    /**
     * Execute the given operation.
     *
     * @param string $operation The name of the operation to execute.
     * @param array $params The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute($operation, array $params = array())
    {
        if ($this->sessionId === null || $this->sessionId === -1) {
            $this->open();
        }
        $params['sessionId'] = $this->sessionId;
        $params['database'] = $this;
        return $this->client->execute($operation, $params);
    }

    /**
     * Open the database.
     */
    protected function open()
    {
        $response = $this->client->execute('dbOpen', [
            'database' => $this->name,
            'type' => $this->type,
            'username' => $this->username,
            'password' => $this->password
        ]);
        $this->sessionId = $response['sessionId'];
        $this->clusters = new ClusterList($this, $response['clusters']);
    }

    /**
     * @param string $query The query text.
     * @param array $params The query parameters.
     *
     * @return ResultList The result of the query
     */
    public function query($query, array $params = [])
    {
        if (!is_object($query)) {
            $query = Command::fromConfig([
                'text' => $query,
                'params' => $params
            ]);
        }
        else if ($query instanceof Query) {
            $query = $query->prepare();
        }
        $results = $this->execute('command', [
            'query' => $query
        ]);
        if (!is_array($results)) {
            $results = [$results];
        }
        return new ResultList($this, $results);
    }

    /**
     * Creates a select query.
     *
     * @return Query The query object.
     */
    public function select()
    {
        $query = $this->createQuery();
        return call_user_func_array([$query, 'select'], func_get_args());
    }


    /**
     * Creates a traverse query.
     *
     * @return Query The query object.
     */
    public function traverse()
    {
        $query = $this->createQuery();
        return call_user_func_array([$query, 'traverse'], func_get_args());
    }

    /**
     * Creates an insert query.
     *
     * @return Query The query object.
     */
    public function insert()
    {
        $query = $this->createQuery();
        return call_user_func_array([$query, 'insert'], func_get_args());
    }

    /**
     * Creates an update query.
     *
     * @return Query The query object.
     */
    public function update()
    {
        $query = $this->createQuery();
        return call_user_func_array([$query, 'update'], func_get_args());
    }

    /**
     * Creates a delete query.
     *
     * @return Query The query object.
     */
    public function delete()
    {
        $query = $this->createQuery();
        return call_user_func_array([$query, 'delete'], func_get_args());
    }

    /**
     * Create a query for the database.
     * @return Query The query object.
     */
    public function createQuery()
    {
        return new Query($this);
    }

    /**
     * Create a record instance for the given OrientDB class.
     *
     * @param ClassInterface|string $orientClass The OrientDB class.
     * @param array $properties The properties for the record.
     *
     * @return RecordInterface The instantiated record.
     */
    public function createRecordInstance($orientClass = null, array $properties = [])
    {
        return $this->createRecordInstanceInternal($orientClass, 'OrientDB\Records\Record', $properties);
    }

    /**
     * Create a document instance for the given OrientDB class.
     *
     * @param ClassInterface|string $orientClass The OrientDB class.
     * @param array $properties The properties for the document.
     *
     * @return DocumentInterface The instantiated record.
     */
    public function createDocumentInstance($orientClass = null, array $properties = [])
    {
        return $this->createRecordInstanceInternal($orientClass, 'OrientDB\Records\Document', $properties);
    }

    /**
     * Create a record or document instance for the given OrientDB class.
     *
     * @param ClassInterface|string $orientClass The OrientDB class.
     * @param string $defaultPHPClass The name of the default PHP class.
     * @param array $properties The properties for the record or document.
     *
     * @return RecordInterface|DocumentInterface The record or document instance.
     */
    protected function createRecordInstanceInternal($orientClass, $defaultPHPClass, array $properties)
    {
        if ($orientClass === null) {
            return new $defaultPHPClass($this, $properties);
        }
        if ($orientClass instanceof ClassInterface) {
            $orientClass = $orientClass->name;
        }
        if (isset($this->classHandlers[$orientClass])) {
            $handler = $this->classHandlers[$orientClass];
            if (is_string($handler)) {
                return new $handler($this, $properties);
            }
            else {
                return $handler($orientClass, $this, $properties);
            }
        }
        else {
            return new $defaultPHPClass($this, $properties);
        }
    }

    /**
     * Register a PHP class for the given OrientDB class.
     *
     * @param string $orientClass The name of the OrientDB class.
     * @param string|callable $phpClass The name of the PHP class, or a callable which can return a class instance.
     *
     * @return $this The current object, with handler applied.
     */
    public function registerClassHandler($orientClass, $phpClass)
    {
        $this->classHandlers[$orientClass] = $phpClass;
        return $this;
    }

    /**
     * Register an array of class handlers.
     *
     * @param array $classes A map of class names to handlers
     *
     * @return $this The current object, with handlers applied.
     */
    public function registerClassHandlers($classes)
    {
        foreach($classes as $orientClass => $phpClass) {
            $this->classHandlers[$orientClass] = $phpClass;
        }
        return $this;
    }

    /**
     * Unregister a class handler for the given OrientDB class.
     *
     * @param string $orientClass The name of the OrientDB class.
     *
     * @return $this The current object, with the handler removed.
     */
    public function unregisterClassHandler($orientClass)
    {
        unset($this->classHandlers[$orientClass]);
        return $this;
    }
}
