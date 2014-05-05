<?php

namespace Oriento;

class ClientTest extends TestCase
{
    public function testConfigure()
    {
        $client = new Client();
        $client->configure([
            'port' => 2424,
            'transport' => 'binary'
        ]);
        $transport = $client->getTransport();
        $this->assertInstanceOf('Oriento\\Protocols\\Binary\\Transport', $transport);
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
}
