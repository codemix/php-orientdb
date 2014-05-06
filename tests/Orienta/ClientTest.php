<?php

namespace Orienta;

class ClientTest extends DbTestCase
{


    public function testGetDatabases()
    {
        $dbs = $this->client->getDatabases();
        $this->assertArrayHasKey('orienta_clienttest', $dbs);
        $this->assertInstanceOf('Orienta\\Database\\Database', $dbs['orienta_clienttest']);
    }
}
