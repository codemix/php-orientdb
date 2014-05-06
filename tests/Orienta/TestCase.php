<?php

namespace Orienta;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array the test server config
     */
    protected function getConfig()
    {
        return json_decode(file_get_contents(__DIR__.'/../test-server.json'), true);
    }

    protected function createClient()
    {
        $config = $this->getConfig();
        $client = new Client();
        $client->configure([
            'username' => $config['username'],
            'password' => $config['password'],
            'hostname' => $config['host'],
            'port' => $config['port'],
        ]);
        return $client;
    }
}
