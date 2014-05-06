<?php

namespace Oriento\Database;

use Oriento\Client;
use Oriento\Common\ConfigurableInterface;
use Oriento\Common\ConfigurableTrait;

class Database implements ConfigurableInterface
{
    use ConfigurableTrait;

    /**
     * @var string The name of the database.
     */
    public $name;

    /**
     * @var string The database storage type.
     */
    public $storage = 'plocal';

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
     * Execute the given operation.
     *
     * @param string $operation The name of the operation to execute.
     * @param array $params The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute($operation, array $params = array())
    {
        $params['sessionId'] = $this->sessionId;
        return $this->client->execute($operation, $params);
    }
}
