<?php

namespace Orienta\Protocols\Binary\Operations;

use Orienta\Databases\Database;

abstract class AbstractDbOperation extends AbstractOperation
{
    /**
     * @var Database The database this operation is for.
     */
    public $database;
}
