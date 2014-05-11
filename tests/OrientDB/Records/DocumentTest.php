<?php

namespace OrientDB\Records;

use OrientDB\TestCase;

class DocumentTest extends TestCase
{

    public function testArrayAccess()
    {
        $doc = new Document(null, [
            'attributes' => [
                'key1' => 'value 1',
                'key2' => 'value 2'
            ]
        ]);

        $this->assertEquals('value 1', $doc['key1']);
        $this->assertEquals('value 2', $doc['key2']);

        $this->assertEquals(2, count($doc));
    }
}
