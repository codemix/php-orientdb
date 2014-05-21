<?php

namespace OrientDB\Databases;

use OrientDB\DbTestCase;
use OrientDB\Records\Document;
use OrientDB\Records\RecordInterface;

class DatabaseTest extends DbTestCase
{

    public function testClusters()
    {
        $this->assertGreaterThan(3, count(static::$db->getClusters()));
    }

    public function testGetCluster()
    {
        $cluster = static::$db->getCluster('ouser');
        $this->assertEquals($cluster, static::$db->clusters->ouser);
        $this->assertEquals('ouser', $cluster->name);
    }

    public function testLoadRecord()
    {
        $record = static::$db->loadRecord('#5:0');
        $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $record);
        $this->assertEquals(static::$db->classes->OUser, $record->getClass());
        $this->assertEquals('admin', $record->name);
        $this->assertInstanceOf('OrientDB\Records\ID', $record->roles[0]);
    }

    public function testLoadRecordMissing()
    {
        $record = static::$db->loadRecord('#5:11111111110');
        $this->assertNull($record);
    }

    public function testLoadRecordFetchPlan()
    {
        $record = static::$db->loadRecord('#5:0', [
            'fetchPlan' => 'roles:2'
        ]);
        $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $record);
        $this->assertEquals(static::$db->classes->OUser, $record->getClass());
        $this->assertEquals('admin', $record->name);
        $this->assertGreaterThan(0, count($record->roles));
        foreach($record->roles as $role /* @var Document $role */) {
            $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $role);
            $this->assertEquals(static::$db->classes->ORole, $role->getClass());
        }
    }


    public function testSelect()
    {
        $query = static::$db->select('*')->from('OUser')->where(['status' => 'ACTIVE']);
        $results = $query->all();
        $this->assertGreaterThanOrEqual(3, count($results));
    }

    public function testTraverse()
    {
        $query = static::$db->traverse()->from('OUser');
        $results = $query->all();
        $this->assertGreaterThanOrEqual(3, count($results));
        foreach($results as $result /* @var \OrientDB\Records\DocumentInterface $result */) {
            $this->assertInstanceOf('\OrientDB\Records\DocumentInterface', $result);
        }
    }

    public function testInsert()
    {
        $query = static::$db->insert('name = "nom"')->into('OUser');
        $this->assertEquals('INSERT INTO OUser SET name = "nom"', $query->getText());
    }

    public function testDbCountRecord()
    {
        $result = static::$db->execute('dbCountRecords', [
            'storage' => 'memory'
        ]);
        $this->assertGreaterThan(0, $result);
    }
}
