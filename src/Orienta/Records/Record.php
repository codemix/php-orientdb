<?php

namespace Orienta\Records;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Databases\Database;

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
    public function __construct(Database $database, array $config = [])
    {
        $this->database = $database;
        $this->configure($config);
    }
}
