<?php

namespace Oriento\Record;

use Oriento\TestCase;

class DeserializerTest extends TestCase
{

    public function testDeserializeNoClass()
    {
        $result = Deserializer::deserialize('foo:"bar"');
        $this->assertEquals(['@type' => 'd', 'foo' => 'bar'], $result);
    }

    public function testDeserializeWithClass()
    {
        $result = Deserializer::deserialize('MyClass@foo:"bar"');
        $this->assertEquals(['@type' => 'd', '@class' => 'MyClass', 'foo' => 'bar'], $result);
    }

    public function testDeserializeMultipleFields()
    {
        $result = Deserializer::deserialize('foo1:"bar1",foo2: "bar2"');
        $this->assertEquals(['@type' => 'd', 'foo1' => 'bar1', 'foo2' => 'bar2'], $result);
    }

    public function testDeserializeInteger()
    {
        $result = Deserializer::deserialize('foo:1');
        $this->assertEquals(['@type' => 'd', 'foo' => 1], $result);
    }

    public function testDeserializeRID()
    {
        $result = Deserializer::deserialize('foo:#12:10');
        $this->assertEquals(['@type' => 'd', 'foo' => new ID(12, 10)], $result);
    }

    public function testDeserializeArray()
    {
        $result = Deserializer::deserialize('foo:[1, 2, #12:10]');
        $this->assertEquals(['@type' => 'd', 'foo' => [1, 2, new ID(12, 10)]], $result);
    }

    public function testDeserializeSet()
    {
        $result = Deserializer::deserialize('foo:<1, 2, #12:10>');
        $this->assertEquals(['@type' => 'd', 'foo' => [1, 2, new ID(12, 10)]], $result);
    }

    public function testDeserializeMap()
    {
        $result = Deserializer::deserialize('foo:{a: 1, b:2, c: #12:10}');
        $this->assertEquals(['@type' => 'd', 'foo' => ['a' => 1, 'b' => 2, 'c' => new ID(12, 10)]], $result);
    }

    public function testDeserializeEmbeddedRecord()
    {
        $result = Deserializer::deserialize('foo:(a: 1, b:2, c: #12:10)');
        $this->assertEquals([
            '@type' => 'd',
            'foo' => [
                '@type' => 'd',
                'a' => 1,
                'b' => 2,
                'c' => new ID(12, 10)
            ]
        ], $result);
    }


    public function testDeserializeEmbeddedRecordWithClass()
    {
        $result = Deserializer::deserialize('foo:(bar@a: 1, b:2, c: #12:10)');
        $this->assertEquals([
            '@type' => 'd',
            'foo' => [
                '@type' => 'd',
                '@class' => 'bar',
                'a' => 1,
                'b' => 2,
                'c' => new ID(12, 10)
            ]
        ], $result);
    }
}
