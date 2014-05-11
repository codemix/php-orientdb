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


    public function testSelect()
    {
        $query = $this->db->select('*')->from('OUser')->where(['status' => 'ACTIVE']);
        $results = $query->all();
        $this->assertGreaterThanOrEqual(3, count($results));
    }

    public function testTraverse()
    {
        $query = $this->db->traverse()->from('OUser');
        $results = $query->all();
        $this->assertGreaterThanOrEqual(3, count($results));
        foreach($results as $result /* @var \Orienta\Records\DocumentInterface $result */) {
            $this->assertInstanceOf('\Orienta\Records\DocumentInterface', $result);
        }
    }

    public function testInsert()
    {
        $query = $this->db->insert('name = "nom"')->into('OUser');
        $this->assertEquals('INSERT INTO OUser SET name = "nom"', $query->getText());
    }

}
