<?php

namespace OrientDB\Queries;

use OrientDB\DbTestCase;

class ResultListTest extends DbTestCase
{

    public function testScalar()
    {
        $results = $this->db->query('SELECT COUNT(*) FROM OUser');
        $this->assertGreaterThanOrEqual(3, $results->scalar());
    }

    public function testOne()
    {
        $result = $this->db->query('SELECT * FROM OUser')->one();
        $this->assertInstanceOf('OrientDB\Records\Document', $result);
    }
}
