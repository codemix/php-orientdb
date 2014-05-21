<?php

namespace OrientDB\Records;

use OrientDB\DbTestCase;

class DocumentTest extends DbTestCase
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

    public function testLoad()
    {
        $class = static::$db->getClass('OUser');
        $doc = $class->load(0);
        $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $doc);
        $this->assertEquals($class->defaultClusterId, $doc->getId()->cluster);
        $this->assertEquals(0, $doc->getId()->position);
    }

    public function testLifecycle()
    {
        $class = static::$db->getClass('OUser');
        $doc = $class->createDocument();

        $doc->name = "testuser";
        $doc->password = "testpassword";
        $doc->status = 'ACTIVE';

        $this->assertTrue($doc->getIsNew());

        $doc->save();


        $this->assertFalse($doc->getIsNew());

        $this->assertGreaterThan(2, $doc->getId()->position);

        $version = $doc->getVersion();

        $doc->name = "testuser2";

        $doc->save();

        $this->assertGreaterThan($version, $doc->getVersion());
        $clone = $class->load($doc->getId());
        $this->assertInstanceOf('OrientDB\Records\DocumentInterface', $clone);
        $this->assertEquals($doc->name, $clone->name);
        $this->assertFalse($doc->getIsDeleted());

        $doc->delete();

        $this->assertTrue($doc->getIsDeleted());

        $clone = $class->load($doc->getId());
        $this->assertNull($clone);
    }

}
