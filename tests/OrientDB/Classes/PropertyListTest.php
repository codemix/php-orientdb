<?php

namespace OrientDB\Classes;

use OrientDB\DbTestCase;

class PropertyListTest extends DbTestCase
{
    /**
     * @var ClassInterface
     */
    public $class;

    public function testCount()
    {
        $properties = $this->class->properties;
        $this->assertGreaterThan(1, count($properties));
    }

    public function testList()
    {
        $properties = $this->class->properties;
        $this->assertInstanceOf('OrientDB\Classes\PropertyInterface', $properties->name);
    }

    public function testIterator()
    {
        foreach($this->class->properties as $key => $value) {
            $this->assertInstanceOf('OrientDB\Classes\PropertyInterface', $value);
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->class = static::$db->getClass('OUser');
    }
}
