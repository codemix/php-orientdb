<?php

namespace Oriento;

class ClientTest extends DbTestCase
{

    public function testGetDatabases()
    {
        $dbs = $this->client->getDatabases();
        $this->assertArrayHasKey('oriento_clienttest', $dbs);
        $this->assertInstanceOf('Oriento\\Database\\Database', $dbs['oriento_clienttest']);
    }
}
