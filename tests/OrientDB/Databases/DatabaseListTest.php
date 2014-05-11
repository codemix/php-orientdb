<?php

namespace OrientDB\Databases;

use OrientDB\DbTestCase;

class DatabaseListTest extends DbTestCase
{
    public function testCreate()
    {
        $db = $this->client->getDatabases()->create('orientdb_clienttest_create', 'memory');
        $this->assertInstanceOf('OrientDB\Databases\Database', $db);
        $this->assertEquals('orientdb_clienttest_create', $db->name);
    }

    public function testExists()
    {
        $this->assertTrue($this->client->getDatabases()->exists('orientdb_clienttest_create', 'memory'));
        $this->assertFalse($this->client->getDatabases()->exists('orientdb_clienttest_MISSING', 'memory'));
    }

    public function testDrop()
    {
        $ok = $this->client->getDatabases()->drop('orientdb_clienttest_create', 'memory');
        $this->assertTrue($ok);
    }
}
