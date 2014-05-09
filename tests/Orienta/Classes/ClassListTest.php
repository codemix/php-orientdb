<?php

namespace Orienta\Classes;

use Orienta\DbTestCase;

class ClassListTest extends DbTestCase
{
    public function testList()
    {
        $classes = $this->db->getClasses();
        $this->assertInstanceOf('Orienta\Classes\BuiltinClass', $classes->OUser);
        $this->assertInstanceOf('Orienta\Classes\BuiltinClass', $classes->ORole);
    }

    public function testIterator()
    {
        $this->assertGreaterThan(0, count($this->db->getClasses()));
        foreach($this->db->getClasses() as $key => $value) {
            $this->assertInstanceOf('Orienta\Classes\ClassInterface', $value);
        }
    }


}
