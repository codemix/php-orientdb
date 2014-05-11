<?php

namespace OrientDB\Records;

use OrientDB\TestCase;

class IDTest extends TestCase
{


    public function testParseString()
    {
        $record = new ID("#12:10");
        $this->assertEquals(12, $record->cluster);
        $this->assertEquals(10, $record->position);
    }

    public function testUseArray()
    {
        $record = new ID(['cluster' => 12, 'position' => 10]);
        $this->assertEquals(12, $record->cluster);
        $this->assertEquals(10, $record->position);
    }


    public function testNumerical()
    {
        $record = new ID(12, 10);
        $this->assertEquals(12, $record->cluster);
        $this->assertEquals(10, $record->position);
    }

    public function testNumericalStrings()
    {
        $record = new ID("12", "10");
        $this->assertEquals(12, $record->cluster);
        $this->assertEquals(10, $record->position);
    }


    public function testToString()
    {
        $record = new ID(12, 10);
        $this->assertEquals('#12:10', $record.'');
    }

    public function testToJSON()
    {
        $record = new ID(12, 10);
        $this->assertEquals('"#12:10"', json_encode($record));
    }
}
