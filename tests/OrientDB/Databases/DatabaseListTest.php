<?php

namespace OrientDB\Databases;

use OrientDB\DbTestCase;

class DatabaseListTest extends DbTestCase
{
    public function testCreate()
    {
        $db = static::$client->getDatabases()->create('orientdb_clienttest_create', 'memory');
        $this->assertInstanceOf('OrientDB\Databases\Database', $db);
        $this->assertEquals('orientdb_clienttest_create', $db->name);
    }

    public function testExists()
    {
        $this->assertTrue(static::$client->getDatabases()->exists('orientdb_clienttest_create', 'memory'));
        $this->assertFalse(static::$client->getDatabases()->exists('orientdb_clienttest_MISSING', 'memory'));
    }

    public function testDrop()
    {
        $ok = static::$client->getDatabases()->drop('orientdb_clienttest_create', 'memory');
        $this->assertTrue($ok);
    }
}
