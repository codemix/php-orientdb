<?php

namespace Orienta\Queries;

use Orienta\Records\ID;
use Orienta\TestCase;

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
        $this->assertEquals('SELECT name, address', $statement->getText());
    }

    public function testSelectArray()
    {
        $statement = new Statement();
        $statement->select(['name', 'address']);
        $this->assertEquals('SELECT name, address', $statement->getText());
    }

    public function testSelectArrayMap()
    {
        $statement = new Statement();
        $statement->select([
            'nom' => 'name',
            'addr' => 'address',
        ]);
        $this->assertEquals('SELECT name AS nom, address AS addr', $statement->getText());
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
        $this->assertEquals('SELECT * FROM OUser, ORole', $statement->getText());
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

    public function testWhereMap()
    {
        $statement = new Statement();
        $statement->select()->from('OUser')->where([
            'name' => 'admin'
        ]);
        $this->assertEquals('SELECT * FROM OUser WHERE (name = :where_0_name)', $statement->getText());
    }
}
