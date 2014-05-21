<?php

namespace OrientDB\Queries;
use OrientDB\Common\ListInterface;
use OrientDB\Common\ListTrait;
use OrientDB\Databases\Database;
use OrientDB\Records\DocumentInterface;
use OrientDB\Records\RecordInterface;

/**
 * Represents the result of a query.
 *
 * @package OrientDB\Queries
 */
class ResultList implements ListInterface
{
    use ListTrait;

    /**
     * @var Database The database the result list belongs to.
     */
    protected $database;

    /**
     * @param Database $database The database object.
     * @param array $items The results.
     */
    public function __construct(Database $database, array $items = [])
    {
        $this->database = $database;
        $this->items = $items;
    }

    /**
     * Get the first field of the first result.
     *
     * @return mixed
     */
    public function scalar()
    {
        if (!isset($this->items[0])) {
            return null;
        }
        $item = $this->items[0];
        if ($item instanceof DocumentInterface) {
            $attributes = $item->getAttributes();
            return reset($attributes);
        }
        else {
            return $item;
        }
    }

    /**
     * Get the first result.
     *
     * @return RecordInterface|DocumentInterface The first result.
     */
    public function one()
    {
        return isset($this->items[0]) ? $this->items[0] : null;
    }

    public function toArray()
    {
        return $this->items;
    }
}
