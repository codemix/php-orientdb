<?php

namespace OrientDB\Records;

use OrientDB\Common\ConfigurableInterface;
use OrientDB\Common\ConfigurableTrait;
use OrientDB\Databases\Database;

class Record implements RecordInterface, ConfigurableInterface
{
    use ConfigurableTrait;
    use RecordTrait;


    /**
     * # Constructor
     *
     * @param Database $database The database the record belongs to.
     * @param array $config The configuration for the record.
     */
    public function __construct(Database $database = null, array $config = [])
    {
        $this->database = $database;
        $this->configure($config);
    }
}
