<?php

namespace OrientDB\Records;

use OrientDB\Databases\MockDatabase;
use OrientDB\TestCase;

class SerializerTest extends TestCase
{
    public function testSerializeString()
    {
        $input = 'hello world';
        $output = Serializer::serialize($input);
        $this->assertEquals('"hello world"', $output);
    }

    public function testSerializeStringWithQuptes()
    {
        $input = 'hello "world"';
        $output = Serializer::serialize($input);
        $this->assertEquals('"hello \\"world\\""', $output);
    }

    public function testSerializeInteger()
    {
        $input = 123;
        $output = Serializer::serialize($input);
        $this->assertEquals('123', $output);
    }

    public function testSerializeTrue()
    {
        $input = true;
        $output = Serializer::serialize($input);
        $this->assertEquals('true', $output);
    }

    public function testSerializeFalse()
    {
        $input = false;
        $output = Serializer::serialize($input);
        $this->assertEquals('false', $output);
    }

    public function testSerializeNull()
    {
        $input = null;
        $output = Serializer::serialize($input);
        $this->assertEquals('null', $output);
    }

    public function testSerializeFloat()
    {
        $input = 123.456;
        $output = Serializer::serialize($input);
        $this->assertEquals('123.456f', $output);
    }


    public function testSerializeDate()
    {
        $now = time();
        $input = \DateTime::createFromFormat('U', $now);
        $output = Serializer::serialize($input);
        $this->assertEquals($now.'t', $output);
    }

    public function testSerializeFlatArray()
    {
        $input = [1, 2, 3];
        $output = Serializer::serialize($input);
        $this->assertEquals('[1,2,3]', $output);
    }

    public function testSerializeAssociativeArray()
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];
        $output = Serializer::serialize($input);
        $this->assertEquals('{"a":1,"b":2,"c":3}', $output);
    }

    public function testSerializeDocument()
    {
        $input = new Document(new MockDatabase());
        $input->setClass('OUser');
        $input->setAttributes([
            'name' => 'Charles',
            'status' => 'ACTIVE'
        ]);
        $output = Serializer::serialize($input);
        $this->assertEquals('OUser@name:"Charles",status:"ACTIVE"', $output);
    }

    public function testSerializeEmbeddedDocument()
    {
        $doc = new Document(new MockDatabase());
        $doc->setClass('OUser');
        $doc->setAttributes([
            'name' => 'Charles',
            'status' => 'ACTIVE'
        ]);
        $input = ['doc' => $doc];
        $output = Serializer::serialize($input);
        $this->assertEquals('{"doc":(OUser@name:"Charles",status:"ACTIVE")}', $output);
    }
}
