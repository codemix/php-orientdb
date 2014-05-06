<?php

namespace Orienta\Database;

use Orienta\DbTestCase;

class DatabaseListTest extends DbTestCase
{
    public function testCreate()
    {
        $db = $this->client->databases->create('orienta_clienttest_create', 'memory');
        $this->assertInstanceOf('Orienta\\Database\\Database', $db);
        $this->assertEquals('orienta_clienttest_create', $db->name);
    }

    public function testExists()
    {
        $this->assertTrue($this->client->getDatabases()->exists('orienta_clienttest_create', 'memory'));
        $this->assertFalse($this->client->getDatabases()->exists('orienta_clienttest_MISSING', 'memory'));
    }

    public function testDrop()
    {
        $ok = $this->client->databases->drop('orienta_clienttest_create', 'memory');
        $this->assertTrue($ok);
    }
}
