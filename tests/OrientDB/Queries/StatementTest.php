<?php

namespace OrientDB\Queries;

use OrientDB\Records\ID;
use OrientDB\TestCase;

class StatementTest extends TestCase
{

    public function testSelect()
    {
        $statement = new Statement();
        $statement->select();
        $this->assertEquals('SELECT *', $statement->getText());
    }

    public function testSelectVarArgs()
    {
        $statement = new Statement();
        $statement->select('name', 'address');
        $this->assertEquals('SELECT name,address', $statement->getText());
    }

    public function testSelectArray()
    {
        $statement = new Statement();
        $statement->select(['name', 'address']);
        $this->assertEquals('SELECT name,address', $statement->getText());
    }

    public function testSelectArrayMap()
    {
        $statement = new Statement();
        $statement->select([
            'nom' => 'name',
            'addr' => 'address',
        ]);
        $this->assertEquals('SELECT name AS nom,address AS addr', $statement->getText());
    }

    public function testSelectExpression()
    {
        $statement = new Statement();
        $statement->select(new Expression('COUNT(*)'));
        $this->assertEquals('SELECT (COUNT(*))', $statement->getText());
    }

    public function testFrom()
    {
        $statement = new Statement();
        $statement->select()->from('OUser');
        $this->assertEquals('SELECT * FROM OUser', $statement->getText());
    }

    public function testFromVarArgs()
    {
        $statement = new Statement();
        $statement->select()->from('OUser', 'ORole');
        $this->assertEquals('SELECT * FROM OUser,ORole', $statement->getText());
    }

    public function testFromRID()
    {
        $statement = new Statement();
        $statement->select()->from(new ID('#12:10'));
        $this->assertEquals('SELECT * FROM #12:10', $statement->getText());
    }

    public function testWhere()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where('1 = 1');
        $this->assertEquals('SELECT * FROM OUser WHERE (1 = 1)', $statement->getText());
    }

    public function testWhereMulti()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where('1 = 1')->where('2 = 2')->where('3 = 3', 'OR');
        $this->assertEquals('SELECT * FROM OUser WHERE (1 = 1 AND 2 = 2) OR (3 = 3)', $statement->getText());
    }

    public function testWhereMagic()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where('1 = 1')->and('2 = 2')->or('3 = 3');
        $this->assertEquals('SELECT * FROM OUser WHERE (1 = 1 AND 2 = 2) OR (3 = 3)', $statement->getText());
    }


    public function testWhereMap()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where([
            'name' => 'admin'
        ]);
        $this->assertEquals('SELECT * FROM OUser WHERE (name = :paramwhere0name)', $statement->getText());
    }

    public function testWhereMapMany()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where([
            'name' => 'admin',
            'status' => 'ACTIVE'
        ]);
        $this->assertEquals('SELECT * FROM OUser WHERE (name = :paramwhere0name AND status = :paramwhere0status)', $statement->getText());
    }

    public function testGroupBy()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->groupBy('name');
        $this->assertEquals('SELECT * FROM OUser GROUP BY name', $statement->getText());
    }

    public function testGroupByDesc()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->groupBy('status desc');
        $this->assertEquals('SELECT * FROM OUser GROUP BY status desc', $statement->getText());
    }

    public function testGroupByMulti()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->groupBy('name', 'status');
        $this->assertEquals('SELECT * FROM OUser GROUP BY name,status', $statement->getText());
    }

    public function testLimit()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->limit(1);
        $this->assertEquals('SELECT * FROM OUser LIMIT 1', $statement->getText());
    }

    public function testOffset()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->offset(1);
        $this->assertEquals('SELECT * FROM OUser SKIP 1', $statement->getText());
    }

    public function testLimitOffset()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->limit(1)->offset(1);
        $this->assertEquals('SELECT * FROM OUser SKIP 1 LIMIT 1', $statement->getText());
    }

    public function testFetch()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->fetch('roles:1');
        $this->assertEquals('SELECT * FROM OUser FETCHPLAN roles:1', $statement->getText());
    }

    public function testFetchArray()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->fetch([
            'roles' => 1
        ]);
        $this->assertEquals('SELECT * FROM OUser FETCHPLAN roles:1', $statement->getText());
    }

    public function testLock()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->lock();
        $this->assertEquals('SELECT * FROM OUser LOCK default', $statement->getText());
    }

    public function testLockRecord()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->lock('record');
        $this->assertEquals('SELECT * FROM OUser LOCK record', $statement->getText());
    }


    public function testUpdate()
    {
        $statement = new Statement();
        $statement->update('OUser');
        $this->assertEquals('UPDATE OUser', $statement->getText());
    }

    public function testSet()
    {
        $statement = new Statement();
        $statement->update('OUser')->set('status = "ACTIVE"');
        $this->assertEquals('UPDATE OUser SET status = "ACTIVE"', $statement->getText());
    }

    public function testSetMulti()
    {
        $statement = new Statement();
        $statement->update('OUser')->set('status = "ACTIVE"')->set('foo = "bar"');
        $this->assertEquals('UPDATE OUser SET status = "ACTIVE",foo = "bar"', $statement->getText());
    }


    public function testSetArray()
    {
        $statement = new Statement();
        $statement->update('OUser')->set([
            'status' => 'ACTIVE'
        ]);
        $this->assertEquals('UPDATE OUser SET status = :paramsetstatus', $statement->getText());
    }

    public function testSetArrayMulti()
    {
        $statement = new Statement();
        $statement->update('OUser')->set([
            'status' => 'ACTIVE',
            'foo' => 'bar'
        ]);
        $this->assertEquals('UPDATE OUser SET status = :paramsetstatus,foo = :paramsetfoo', $statement->getText());
    }

    public function testInsert()
    {
        $statement = new Statement();
        $statement->insert()->into('OUser');
        $this->assertEquals('INSERT INTO OUser', $statement->getText());
    }

    public function testInsertString()
    {
        $statement = new Statement();
        $statement->insert('foo = "bar"')->into('OUser');
        $this->assertEquals('INSERT INTO OUser SET foo = "bar"', $statement->getText());
    }

    public function testInsertArray()
    {
        $statement = new Statement();
        $statement->insert(['foo' => 'bar'])->into('OUser');
        $this->assertEquals('INSERT INTO OUser SET foo = :paramsetfoo', $statement->getText());
    }

    public function testDelete()
    {
        $statement = new Statement();
        $statement->delete('OUser');
        $this->assertEquals('DELETE FROM OUser', $statement->getText());
    }

    public function testDeleteWhere()
    {
        $statement = new Statement();
        $statement->delete('OUser')->where('1=1');
        $this->assertEquals('DELETE FROM OUser WHERE (1=1)', $statement->getText());
    }

    public function testCreateClass()
    {
        $statement = new Statement();
        $statement->create()->class('MyClass');
        $this->assertEquals('CREATE CLASS MyClass', $statement->getText());
    }

    public function testCreateClassExtends()
    {
        $statement = new Statement();
        $statement->create()->class('MyClass')->extends('ORestricted');
        $this->assertEquals('CREATE CLASS MyClass EXTENDS ORestricted', $statement->getText());
    }

    public function testCreateClassExtendsMycluster()
    {
        $statement = new Statement();
        $statement->create()->class('MyClass')->extends('ORestricted')->cluster(1);
        $this->assertEquals('CREATE CLASS MyClass EXTENDS ORestricted CLUSTER 1', $statement->getText());
    }

    public function testCreateVertex()
    {
        $statement = new Statement();
        $statement->create()->vertex('V')->set('a=1');
        $this->assertEquals('CREATE VERTEX V SET a=1', $statement->getText());
    }

    public function testCreateVertexCluster()
    {
        $statement = new Statement();
        $statement->create()->vertex('V')->cluster('mycluster')->set('a=1');
        $this->assertEquals('CREATE VERTEX V CLUSTER mycluster SET a=1', $statement->getText());
    }

    public function testCreateVertexContent()
    {
        $statement = new Statement();
        $statement->create()->vertex('V')->content(['a' => 1]);
        $this->assertEquals('CREATE VERTEX V CONTENT {"a":1}', $statement->getText());
    }

    public function testCreateEdge()
    {
        $statement = new Statement();
        $statement->create()->edge('E')->from('#1:1')->to('#1:2');
        $this->assertEquals('CREATE EDGE E FROM #1:1 TO #1:2', $statement->getText());
    }

    public function testCreateEdgeCluster()
    {
        $statement = new Statement();
        $statement->create()->edge('E')->cluster('mycluster')->from('#1:1')->to('#1:2');
        $this->assertEquals('CREATE EDGE E CLUSTER mycluster FROM #1:1 TO #1:2', $statement->getText());
    }

    public function testCreateEdgeSet()
    {
        $statement = new Statement();
        $statement->create()->edge('E')->from('#1:1')->to('#1:2')->set('a=1');
        $this->assertEquals('CREATE EDGE E FROM #1:1 TO #1:2 SET a=1', $statement->getText());
    }

}
