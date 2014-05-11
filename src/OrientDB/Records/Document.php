<?php

namespace OrientDB\Records;

use OrientDB\Common\ConfigurableInterface;
use OrientDB\Common\ConfigurableTrait;
use OrientDB\Databases\Database;

class Document implements DocumentInterface, ConfigurableInterface
{
    use ConfigurableTrait;
    use DocumentTrait;


    /**
     * # Constructor
     *
     * @param Database $database The database the document belongs to.
     * @param array $config The configuration for the record.
     */
    public function __construct(Database $database = null, array $config = [])
    {
        $this->database = $database;
        $this->configure($config);
    }


}
