<?php

namespace Orienta\Database;

use Orienta\DbTestCase;

class DatabaseTest extends DbTestCase
{
    /**
     * @var Database
     */
    public $db;

    protected function setUp()
    {
        parent::setUp();
        $this->db = $this->client->getDatabase($this->getDbName());
    }


    public function testQuery()
    {
        $results = $this->db->query('SELECT * FROM OUser');
        $this->assertArrayHasKey(0, $results);
        $this->assertArrayHasKey('content', $results[0]);
        $this->assertGreaterThanOrEqual(3, count($results[0]['content']));
    }
}
