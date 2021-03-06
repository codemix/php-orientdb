<?php

namespace OrientDB\Queries;

use OrientDB\DbTestCase;

class ResultListTest extends DbTestCase
{

    public function testScalar()
    {
        $results = static::$db->query('SELECT COUNT(*) FROM OUser');
        $this->assertGreaterThanOrEqual(3, $results->scalar());
    }

    public function testOne()
    {
        $result = static::$db->query('SELECT * FROM OUser')->one();
        $this->assertInstanceOf('OrientDB\Records\Document', $result);
    }
}
