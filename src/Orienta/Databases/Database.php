<?php

namespace Orienta\Databases;

use Orienta\Classes\ClassList;
use Orienta\Client;
use Orienta\Clusters\Cluster;
use Orienta\Clusters\ClusterList;
use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;
use Orienta\Queries\Sync;
use Orienta\Records\DocumentInterface;
use Orienta\Records\RecordInterface;

/**
 * # Database
 *
 * @property ClusterList $clusters
 *
 * @package Orienta\Databases
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
     * @return \Orienta\Clusters\ClusterList
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
     * @param \Orienta\Classes\ClassList $classes
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
     * @return \Orienta\Classes\ClassList
     */
    public function getClasses()
    {
        if ($this->classes === null) {
            $record = $this->execute('recordLoad', [
                'cluster' => 0,
                'position' => 1
            ]);
            $this->classes = new ClassList($this, $record->classes);
        }
        return $this->classes;
    }

    /**
     * Gets a class with the specific name.
     *
     * @param string $name The name of the class to get.
     *
     * @return null|\Orienta\Classes\ClassInterface The class instance.
     */
    public function getClass($name)
    {
        return $this->getClasses()->offsetGet($name);
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
     * @return mixed The result of the query
     */
    public function query($query, array $params = [])
    {
        if (!is_object($query)) {
            $query = Sync::fromConfig([
                'text' => $query,
                'params' => $params
            ]);
        }
        return $this->execute('command', [
            'query' => $query
        ]);
    }

    /**
     * Create a record instance for the given OrientDB class.
     *
     * @param string $orientClass The name of the OrientDB class.
     * @param array $properties The properties for the record.
     *
     * @return RecordInterface The instantiated record.
     */
    public function createRecordInstance($orientClass, array $properties = [])
    {
        return $this->createRecordInstanceInternal($orientClass, 'Orienta\Records\Record', $properties);
    }

    /**
     * Create a document instance for the given OrientDB class.
     *
     * @param string $orientClass The name of the OrientDB class.
     * @param array $properties The properties for the document.
     *
     * @return DocumentInterface The instantiated record.
     */
    public function createDocumentInstance($orientClass, array $properties = [])
    {
        return $this->createRecordInstanceInternal($orientClass, 'Orienta\Records\Document', $properties);
    }

    /**
     * Create a record or document instance for the given OrientDB class.
     *
     * @param string $orientClass The name of the OrientDB class.
     * @param string $defaultPHPClass The name of the default PHP class.
     * @param array $properties The properties for the record or document.
     *
     * @return RecordInterface|DocumentInterface The record or document instance.
     */
    protected function createRecordInstanceInternal($orientClass, $defaultPHPClass, array $properties)
    {
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
