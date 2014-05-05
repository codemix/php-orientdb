<?php

namespace Oriento;

class ClientTest extends TestCase
{

    public function testConfigure()
    {
        $client = new Client();
        $client->configure([
            'port' => 2525,
            'transport' => 'binary'
        ]);
        $transport = $client->getTransport();
        $this->assertInstanceOf('Oriento\\Protocols\\Binary\\Transport', $transport);
    }


    public function testExecute()
    {
        $client = new Client();
        $client->configure([
            'port' => 2525,
            'transport' => 'binary'
        ]);
        $result = $client->execute('connect', [
            'username' => 'root',
            'password' => 'root'
        ]);

        echo "got ".$result."\n";
    }
}
