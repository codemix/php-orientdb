<?php

namespace OrientDB\Clusters;

use OrientDB\DbTestCase;

class ClusterTest extends DbTestCase
{

    /**
     * @var Cluster
     */
    public $cluster;

    protected function setUp()
    {
        parent::setUp();
        $this->cluster = static::$db->getCluster('ouser');
    }


    public function testCount()
    {
        $this->assertGreaterThan(2, $this->cluster->count());
    }

    public function testLoad()
    {
        $result = $this->cluster->load(0);
        $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $result);
        $this->assertEquals('admin', $result->name);
    }

}
