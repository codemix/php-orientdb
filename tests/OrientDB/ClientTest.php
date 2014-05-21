<?php

namespace OrientDB;

class ClientTest extends DbTestCase
{


    public function testGetDatabases()
    {
        $dbs = static::$client->getDatabases();
        $this->assertArrayHasKey('orientdb_clienttest', $dbs);
        $this->assertInstanceOf('OrientDB\Databases\Database', $dbs['orientdb_clienttest']);
    }
}
