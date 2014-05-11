<?php

namespace Orienta\Classes;

use Orienta\DbTestCase;

class PropertyTest extends DbTestCase
{
    public $class;

    public function testValidateMandatory()
    {
        $property = new Property($this->class, [
            'name' => 'myprop',
            'mandatory' => true,
        ]);

        list($isValid, list($error)) = $property->validate(null);
        $this->assertFalse($isValid);
        $this->assertEquals('myprop is mandatory.', $error);

        list($isValid, list($error)) = $property->validate('');
        $this->assertFalse($isValid);
        $this->assertEquals('myprop is mandatory.', $error);

        list($isValid, $errors) = $property->validate('hello world');
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);
    }

    public function testValidateNotNull()
    {
        $property = new Property($this->class, [
            'name' => 'myprop',
            'notNull' => true,
        ]);

        list($isValid, list($error)) = $property->validate(null);
        $this->assertFalse($isValid);
        $this->assertEquals('myprop cannot be null.', $error);

        list($isValid, $errors) = $property->validate('hello world');
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);
    }

    public function testValidatePattern()
    {
        $property = new Property($this->class, [
            'name' => 'myprop',
            'regexp' => '[M|F]'
        ]);

        list($isValid, list($error)) = $property->validate('G');
        $this->assertFalse($isValid);
        $this->assertEquals('myprop does not match the required pattern.', $error);

        list($isValid, $errors) = $property->validate('M');
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate('F');
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate(null);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);
    }

    public function testMinNumber()
    {
        $property = new Property($this->class, [
            'name' => 'myprop',
            'min' => 3
        ]);

        list($isValid, list($error)) = $property->validate(1);
        $this->assertFalse($isValid);
        $this->assertEquals('myprop must be at least 3.', $error);

        list($isValid, $errors) = $property->validate(3);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate(500);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate(null);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);
    }


    public function testMaxNumber()
    {
        $property = new Property($this->class, [
            'name' => 'myprop',
            'max' => 3
        ]);

        list($isValid, list($error)) = $property->validate(100);
        $this->assertFalse($isValid);
        $this->assertEquals('myprop must be at most 3.', $error);

        list($isValid, $errors) = $property->validate(3);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate(-123);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);

        list($isValid, $errors) = $property->validate(null);
        $this->assertTrue($isValid);
        $this->assertEquals([], $errors);
    }


    protected function setUp()
    {
        parent::setUp();
        $this->class = $this->db->getClass('OUser');
    }


}
