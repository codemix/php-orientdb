<?php

namespace Orienta\Databases;

use Orienta\DbTestCase;

class DatabaseTest extends DbTestCase
{

    public function testClusters()
    {
        $this->assertGreaterThan(3, count($this->db->getClusters()));
    }

    public function testGetCluster()
    {
        $cluster = $this->db->getCluster('ouser');
        $this->assertEquals($cluster, $this->db->clusters->ouser);
        $this->assertEquals('ouser', $cluster->name);
    }

    public function testQuery()
    {
        $results = $this->db->query('SELECT * FROM OUser');
        $this->assertArrayHasKey(0, $results);
        $this->assertArrayHasKey('content', $results[0]);
        $this->assertGreaterThanOrEqual(3, count($results[0]['content']));
    }
}
