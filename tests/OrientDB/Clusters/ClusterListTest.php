<?php

namespace OrientDB\Clusters;

use OrientDB\DbTestCase;

class ClusterListTest extends DbTestCase
{
    public function testCreateExistsThenDrop()
    {
        $this->assertFalse(static::$db->clusters->exists('mycluster'));

        $count = static::$db->clusters->count();
        $result = static::$db->clusters->create('mycluster', 'MEMORY', ['type' => 'MEMORY']);
        $this->assertInstanceOf('OrientDB\Clusters\Cluster', $result);
        $this->assertEquals('mycluster', $result->name);
        $this->assertEquals($count + 1, static::$db->clusters->count());

        $this->assertTrue(static::$db->clusters->exists('mycluster'));

        $this->assertTrue(isset(static::$db->clusters->mycluster));
        $cluster = static::$db->clusters->mycluster;
        static::$db->clusters->drop($cluster);

        $this->assertFalse(isset(static::$db->clusters->mycluster));
        $this->assertFalse(static::$db->clusters->exists('mycluster'));
    }

    public function testIterator()
    {
        $this->assertGreaterThan(0, count(static::$db->clusters));
        foreach(static::$db->clusters as $key => $value) {
            $this->assertInstanceOf('OrientDB\Clusters\Cluster', $value);
        }
    }


}
