<?php

namespace OrientDB\Classes;

use OrientDB\DbTestCase;

class ClassListTest extends DbTestCase
{
    public function testList()
    {
        $classes = static::$db->getClasses();
        $this->assertInstanceOf('OrientDB\Classes\BuiltinClass', $classes->OUser);
        $this->assertInstanceOf('OrientDB\Classes\BuiltinClass', $classes->ORole);
    }

    public function testIterator()
    {
        $this->assertGreaterThan(0, count(static::$db->getClasses()));
        foreach(static::$db->getClasses() as $key => $value) {
            $this->assertInstanceOf('OrientDB\Classes\ClassInterface', $value);
        }
    }

    public function testById()
    {
        $class = static::$db->getClasses()->byId(5);
        $this->assertInstanceOf('OrientDB\Classes\BuiltinClass', $class);
        $this->assertEquals('OUser', $class->name);
    }

}
