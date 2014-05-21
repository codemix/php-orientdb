<?php

namespace OrientDB\Protocols\Binary;

use OrientDB\Client;
use OrientDB\TestCase;

class TransportTest extends TestCase
{
    public function testConfigure()
    {
        $client = new Client();
        $client->configure([
            'port' => 2424,
            'transport' => 'binary'
        ]);
        $transport = $client->getTransport();
        $this->assertInstanceOf('OrientDB\Protocols\Binary\Transport', $transport);
    }

    /**
     * @expectedException \OrientDB\Exceptions\Exception
     */
    public function testConnectWithBadCredentials()
    {
        $client = $this->createClient();
        $sessionId = $client->execute('connect', [
            'username' => $client->username,
            'password' => 'thisisnottherightpassword!'
        ]);

        $this->assertGreaterThan(0, $sessionId);
    }

    public function testConnect()
    {
        $client = $this->createClient();
        $sessionId = $client->execute('connect', [
            'username' => $client->username,
            'password' => $client->password
        ]);

        $this->assertGreaterThan(0, $sessionId);
    }

    public function testDbList()
    {
        $client = $this->createClient();
        $sessionId = $client->execute('connect', [
            'username' => $client->username,
            'password' => $client->password
        ]);

        $result = $client->execute('dbList', [
           'sessionId' => $sessionId
        ]);

        $this->assertArrayHasKey('databases', $result);
        $this->assertGreaterThan(0, count($result['databases']));
    }

    public function testDbExists()
    {
        $client = $this->createClient();
        $sessionId = $client->execute('connect', [
            'username' => $client->username,
            'password' => $client->password
        ]);

        $result = $client->execute('dbExists', [
            'sessionId' => $sessionId,
            'database' => 'GratefulDeadConcerts'
        ]);

        $this->assertTrue($result);

        $result = $client->execute('dbExists', [
            'sessionId' => $sessionId,
            'database' => 'missing_db'
        ]);

        $this->assertFalse($result);
    }

    public function testDbOpen()
    {
        $client = $this->createClient();
        $result = $client->execute('dbOpen', [
            'database' => 'GratefulDeadConcerts',
            'type' => 'graph',
            'username' => 'admin',
            'password' => 'admin',
        ]);

        $this->assertArrayHasKey('clusters', $result);
        $this->assertGreaterThan(5, count($result['clusters']));
    }

    public function testDbFreeze()
    {
        $client = $this->createClient();
        $result = $client->execute('dbFreeze', [
            'database' => 'GratefulDeadConcerts',
            'storage' => 'memory'
        ]);
        $this->assertTrue($result);
    }

    public function testDbRelease()
    {
        $client = $this->createClient();
        $result = $client->execute('dbRelease', [
            'database' => 'GratefulDeadConcerts',
            'storage' => 'memory'
        ]);
        $this->assertTrue($result);
    }

}
