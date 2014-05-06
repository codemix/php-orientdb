<?php

namespace Oriento;

class ClientTest extends DbTestCase
{
    public function testCreateDatabase()
    {
        $db = $this->client->create('oriento_clienttest_create', 'memory');
        $this->assertInstanceOf('Oriento\\Database\\Database', $db);
        $this->assertEquals('oriento_clienttest_create', $db->name);
    }

    public function testGetDatabases()
    {
        $dbs = $this->client->getDatabases();
        $this->assertArrayHasKey('oriento_clienttest', $dbs);
        $this->assertInstanceOf('Oriento\\Database\\Database', $dbs['oriento_clienttest']);
    }
}
