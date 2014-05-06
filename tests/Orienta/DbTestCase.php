<?php

namespace Orienta;

use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;

class DbTestCase extends TestCase implements MagicInterface
{
    use MagicTrait;

    /**
     * @var Client
     */
    public $client;

    /**
     * @var string
     */
    private $dbName;

    /**
     * Gets the DbName
     * @return string
     */
    public function getDbName()
    {
        if ($this->dbName === null) {
            $this->dbName = strtolower(str_replace('\\','_', get_called_class()));
        }
        return $this->dbName;
    }

    protected function setUp()
    {
        $this->client = $this->createClient();
        if ($this->client->getDatabases()->exists($this->getDbName(), 'memory')) {
            $this->client->getDatabases()->drop($this->getDbName(), 'memory');
        }
        $this->client->getDatabases()->create($this->getDbName(), 'memory');
    }

    protected function tearDown()
    {
        if ($this->client->getDatabases()->exists($this->getDbName(), 'memory')) {
            $this->client->getDatabases()->drop($this->getDbName(), 'memory');
        }
    }


}