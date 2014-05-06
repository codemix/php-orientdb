<?php

namespace Orienta;

class ClientTest extends DbTestCase
{
    public function testCreateDatabase()
    {
        $db = $this->client->create('orienta_clienttest_create', 'memory');
        $this->assertInstanceOf('Orienta\\Database\\Database', $db);
        $this->assertEquals('orienta_clienttest_create', $db->name);
    }

    public function testGetDatabases()
    {
        $dbs = $this->client->getDatabases();
        $this->assertArrayHasKey('orienta_clienttest', $dbs);
        $this->assertInstanceOf('Orienta\\Database\\Database', $dbs['orienta_clienttest']);
    }
}
