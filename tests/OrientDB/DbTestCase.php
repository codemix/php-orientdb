<?php

namespace OrientDB;

use OrientDB\Common\MagicInterface;
use OrientDB\Common\MagicTrait;
use OrientDB\Databases\Database;

class DbTestCase extends TestCase implements MagicInterface
{
    use MagicTrait;

    /**
     * @var Client
     */
    public static $client;

    /**
     * @var Database
     */
    public static $db;


    protected static $dbStorage = 'memory';

    /**
     * Gets the DbName
     * @return string
     */
    public static function getDbName()
    {
        return strtolower(str_replace('\\','_', get_called_class()));
    }

    public static function setUpBeforeClass()
    {
        static::$client = static::createClient();
        if (static::$client->getDatabases()->exists(static::getDbName(), static::$dbStorage)) {
            static::$client->getDatabases()->drop(static::getDbName(), static::$dbStorage);
        }
        static::$db = static::$client->getDatabases()->create(static::getDbName(), static::$dbStorage);
    }

    public static function tearDownAfterClass()
    {
        if (static::$client->getDatabases()->exists(static::getDbName(), static::$dbStorage)) {
            static::$client->getDatabases()->drop(static::getDbName(), static::$dbStorage);
        }
    }


}
