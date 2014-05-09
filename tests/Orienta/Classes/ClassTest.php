<?php

namespace Orienta\Classes;

use Orienta\DbTestCase;

class ClassTest extends DbTestCase
{
    /**
     * @var ClassInterface
     */
    public $class;

    public function testGetProperties()
    {
        $this->assertInstanceOf('Orienta\Classes\PropertyList', $this->class->getProperties());
        $this->assertInstanceOf('Orienta\Classes\Property', $this->class->getProperties()->name);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->class = $this->db->getClass('OUser');
    }

}
