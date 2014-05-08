<?php

namespace Orienta\Clusters;

use Orienta\DbTestCase;

class ClusterListTest extends DbTestCase
{
    public function testCreateExistsThenDrop()
    {
        $this->assertFalse($this->db->clusters->exists('mycluster'));

        $count = $this->db->clusters->count();
        $result = $this->db->clusters->create('mycluster', 'MEMORY', ['type' => 'MEMORY']);
        $this->assertInstanceOf('Orienta\Clusters\Cluster', $result);
        $this->assertEquals('mycluster', $result->name);
        $this->assertEquals($count + 1, $this->db->clusters->count());

        $this->assertTrue($this->db->clusters->exists('mycluster'));

        $this->assertTrue(isset($this->db->clusters->mycluster));
        $cluster = $this->db->clusters->mycluster;
        $this->db->clusters->drop($cluster);

        $this->assertFalse(isset($this->db->clusters->mycluster));
        $this->assertFalse($this->db->clusters->exists('mycluster'));
    }

    public function testIterator()
    {
        $this->assertGreaterThan(0, count($this->db->clusters));
        foreach($this->db->clusters as $key => $value) {
            $this->assertInstanceOf('Orienta\Clusters\Cluster', $value);
        }
    }


}
