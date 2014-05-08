<?php

namespace Orienta\Records;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Databases\Database;

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
    public function __construct(Database $database, array $config = [])
    {
        $this->database = $database;
        $this->configure($config);
    }
}