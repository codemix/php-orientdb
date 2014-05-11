<?php

namespace Orienta\Classes;

use Orienta\DbTestCase;

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
        $this->assertInstanceOf('Orienta\Classes\PropertyList', $this->class->properties);
    }

    public function testGetProperties()
    {
        $this->assertInstanceOf('Orienta\Classes\PropertyList', $this->class->getProperties());
        $this->assertInstanceOf('Orienta\Classes\Property', $this->class->getProperties()->name);
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

    protected function setUp()
    {
        parent::setUp();
        $this->class = $this->db->getClass('OUser');
    }

}
