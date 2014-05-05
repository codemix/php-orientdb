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


}
