<?php

namespace Orienta\Database;

use Orienta\Client;
use Orienta\Cluster\Cluster;
use Orienta\Cluster\ClusterList;
use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;
use Orienta\Query\Sync;

/**
 * # Database
 *
 * @property ClusterList $clusters
 *
 * @package Orienta\Database
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
     * @return \Orienta\Cluster\ClusterList
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
}
