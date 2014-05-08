<?php

namespace Orienta\Protocols\Binary;

use Orienta\Client;
use Orienta\TestCase;
use Orienta\DbTestCase;


class TransportDbTest extends DbTestCase
{

    public function testDbCountRecord()
    {
        $result = $this->db->execute('dbCountRecords', [
            'storage' => 'memory'
        ]);
        $this->assertGreaterThan(0, $result);
    }
    /**
    public function testDbFreeze()
    {

    }

    public function testDbRelease()
    {

    }*/
}
