<?php

namespace Orienta\Queries;

use Orienta\DbTestCase;

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
        $this->assertInstanceOf('Orienta\Records\Document', $result);
    }
}
