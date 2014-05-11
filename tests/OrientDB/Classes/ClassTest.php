<?php

namespace OrientDB\Classes;

use OrientDB\DbTestCase;
use OrientDB\Records\ID;

class ClassTest extends DbTestCase
{
    /**
     * @var ClassInterface
     */
    public $class;

    public function testMagic()
    {
        $this->assertEquals('OUser', $this->class->name);
        $this->assertEquals('', $this->class->shortName);
        $this->assertInstanceOf('OrientDB\Classes\PropertyList', $this->class->properties);
    }

    public function testGetProperties()
    {
        $this->assertInstanceOf('OrientDB\Classes\PropertyList', $this->class->getProperties());
        $this->assertInstanceOf('OrientDB\Classes\Property', $this->class->getProperties()->name);
    }

    public function testValidate()
    {
        list($valid, $errors) = $this->class->validate([
            'name' => 'Charles',
        ]);

        $this->assertFalse($valid);
        $this->assertGreaterThanOrEqual(2, count($errors));
    }

    public function testValidateDocument()
    {
        $doc = $this->class->createDocument([
            'name' => 'Charles',
            'password' => 'password',
        ]);

        list($valid, $errors) = $this->class->validate($doc);

        $this->assertFalse($valid);
        $this->assertEquals(1, count($errors));

        $doc->status = 'ACTIVE';

        list($valid, $errors) = $this->class->validate($doc);

        $this->assertTrue($valid);
        $this->assertEquals([], $errors);
    }

    public function testValidateReadOnly()
    {
        $doc = $this->class->createDocument([
            'name' => 'Charles',
            'password' => 'password',
            'status' => 'ACTIVE'
        ]);

        $doc->setId(new ID(-1, -1));

        list($valid, $errors) = $this->class->validate($doc);

        $this->assertTrue($valid);
        $this->assertEquals([], $errors);

        $this->class->properties->status->readonly = true;
        list($valid, $errors) = $this->class->validate($doc);

        $this->assertFalse($valid);
        $this->assertEquals(1, count($errors));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->class = $this->db->getClass('OUser');
    }

}
