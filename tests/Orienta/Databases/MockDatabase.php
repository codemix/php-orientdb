<?php

namespace Orienta\Databases;

use Orienta\Classes\ClassList;
use Orienta\Client;

class MockDatabase extends Database
{
    public function __construct($client = null, $name = null, $locationString = null)
    {

    }

    public function getClasses()
    {
        if ($this->classes === null) {
            $this->classes = new ClassList($this, [
                'OUser' => [
                    'name' => 'OUser',
                    'defaultClusterId' => 5
                ]
            ]);
        }
        return parent::getClasses(); // TODO: Change the autogenerated stub
    }


}
