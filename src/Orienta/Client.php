<?php

namespace Orienta;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;
use Orienta\Database\Database;
use Orienta\Database\DatabaseList;
use Orienta\Protocols\Common\TransportInterface;

/**
 * Class Client
 *
 * @property \Orienta\Database\DatabaseList $databases
 *
 * @package Orienta
 */
class Client implements ConfigurableInterface, MagicInterface
{
    use ConfigurableTrait;
    use MagicTrait;

    /**
     * @var string The server hostname.
     */
    public $hostname = 'localhost';

    /**
     * @var string The port for the server.
     */
    public $port;

    /**
     * @var string The username for the server.
     */
    public $username = 'root';

    /**
     * @var string The password for the server.
     */
    public $password = 'root';

    /**
     * @var DatabaseList The database objects.
     */
    protected $databases;

    /**
     * @var TransportInterface The transport to use for the connection to the server.
     */
    protected $_transport;

    /**
     * Sets the transport
     *
     * @param \Orienta\Protocols\Common\TransportInterface $transport
     *
     * @return $this the current object
     */
    public function setTransport($transport)
    {
        $this->_transport = $this->createTransport($transport);
        return $this;
    }

    /**
     * Gets the transport
     *
     * @return \Orienta\Protocols\Common\TransportInterface
     */
    public function getTransport()
    {
        if ($this->_transport === null) {
            $this->_transport = $this->createTransport();
        }
        return $this->_transport;
    }

    /**
     * Create a transport instance.
     *
     * @param TransportInterface|array|null $transport
     *
     * @return Protocols\Binary\Transport the transport interface
     */
    protected function createTransport($transport = null)
    {
        if (is_string($transport)) {
            if ($transport === 'binary') {
                $transport = new Protocols\Binary\Transport();
            }
            else {
                $transport = new $transport();
            }
        }
        else if (is_array($transport)) {
            if (isset($transport['class'])) {
                $className = $transport['class'];
                unset($transport['class']);
            }
            else {
                $className = 'Orienta\\Protocols\\Binary\\Transport';
            }
            $transport = new $className();
        }
        else if (!($transport instanceof TransportInterface)) {
            $transport = new Protocols\Binary\Transport();
        }
        $transport->configure([
            'hostname' => $this->hostname,
            'port' => 2424, //$this->port,
            'username' => $this->username,
            'password' => $this->password
        ]);
        return $transport;
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
        return $this->getTransport()->execute($operation, $params);
    }

    /**
     * Gets the Databases
     *
     * @param bool $reload Whether the list of databases should be reloaded from the server.
     *
     * @return \Orienta\Database\DatabaseList
     */
    public function getDatabases($reload = false)
    {
        if ($this->databases === null) {
            $this->databases = new DatabaseList($this);
        }
        if ($reload) {
            $this->databases->reload();
        }
        return $this->databases;
    }

    /**
     * Get a database with the given name.
     *
     * @param string $name The name of the database to get.
     * @param bool $reload Whether to reload the database list.
     *
     * @return null|Database The database instance, or null if it doesn't exist.
     */
    public function getDatabase($name, $reload = false)
    {
        $databases = $this->getDatabases($reload);
        if (isset($databases[$name])) {
            return $databases[$name];
        }
        else {
            return null;
        }
    }



}
