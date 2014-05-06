<?php

namespace Oriento;

use Oriento\Common\MagicInterface;
use Oriento\Common\MagicTrait;

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
        if ($this->client->exists($this->getDbName(), 'memory')) {
            $this->client->drop($this->getDbName(), 'memory');
        }
        $this->client->create($this->getDbName(), 'memory');
    }

    protected function tearDown()
    {
        if ($this->client->exists($this->getDbName(), 'memory')) {
            $this->client->drop($this->getDbName(), 'memory');
        }
    }


}
