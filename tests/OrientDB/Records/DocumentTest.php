<?php

namespace OrientDB\Records;

use OrientDB\DbTestCase;
use OrientDB\Exceptions\Exception;

class DocumentTest extends DbTestCase
{

    protected static $dbStorage = 'plocal';

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

    public function tessstLoad()
    {
        $doc = static::$db->getClass('OUser')->load(1);
    }

    public function testLifecycle()
    {
        $doc = static::$db->getClass('OUser')->createDocument();

        $doc->name = "testuser";
        $doc->password = "testpassword";
        $doc->status = 'ACTIVE';

        $this->assertTrue($doc->getIsNew());

        $this->assertTrue($doc->save());
        $this->assertFalse($doc->getIsNew());

        $this->assertGreaterThan(2, $doc->getId()->position);


        $doc->name = "testuser2";

        $this->assertTrue($doc->save());
    }

}
