<?php

namespace Orienta\Databases;

use Orienta\DbTestCase;
use Orienta\Records\Document;
use Orienta\Records\RecordInterface;

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

    public function testLoadRecord()
    {
        $record = $this->db->loadRecord('#5:0');
        $this->assertInstanceOf('Orienta\Records\DocumentInterface', $record);
        $this->assertEquals($this->db->classes->OUser, $record->getClass());
        $this->assertEquals('admin', $record->name);
        $this->assertInstanceOf('Orienta\Records\ID', $record->roles[0]);
    }

    public function testLoadRecordMissing()
    {
        $record = $this->db->loadRecord('#5:11111111110');
        $this->assertNull($record);
    }

    public function testLoadRecordFetchPlan()
    {
        $record = $this->db->loadRecord('#5:0', [
            'fetchPlan' => 'roles:2'
        ]);
        $this->assertInstanceOf('Orienta\Records\DocumentInterface', $record);
        $this->assertEquals($this->db->classes->OUser, $record->getClass());
        $this->assertEquals('admin', $record->name);
        $this->assertGreaterThan(0, count($record->roles));
        foreach($record->roles as $role /* @var Document $role */) {
            $this->assertInstanceOf('Orienta\Records\DocumentInterface', $role);
            $this->assertEquals($this->db->classes->ORole, $role->getClass());
        }
    }
}
