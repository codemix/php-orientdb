<?php

namespace Orienta\Classes;

use Orienta\DbTestCase;

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
        $this->assertInstanceOf('Orienta\Classes\PropertyInterface', $properties->name);
    }

    public function testIterator()
    {
        foreach($this->class->properties as $key => $value) {
            $this->assertInstanceOf('Orienta\Classes\PropertyInterface', $value);
        }
    }

    protected function setUp()
    {
        parent::setUp();
        $this->class = $this->db->getClass('OUser');
    }
}
