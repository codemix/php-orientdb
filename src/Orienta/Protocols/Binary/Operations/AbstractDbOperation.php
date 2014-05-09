<?php

namespace Orienta\Protocols\Binary\Operations;

use Orienta\Databases\Database;
use Orienta\Exceptions\Exception;
use Orienta\Records\Document;
use Orienta\Records\DocumentInterface;
use Orienta\Records\ID;
use Orienta\Records\Record;
use Orienta\Records\RecordInterface;

abstract class AbstractDbOperation extends AbstractOperation
{
    /**
     * @var Database The database this operation is for.
     */
    public $database;


    /**
     * Normalize a record.
     *
     * @param array $payload The record data.
     *
     * @return array|DocumentInterface|RecordInterface
     */
    protected function normalizeRecord(array $payload)
    {
        if (!isset($payload['type']))
            return $payload;
        if ($payload['cluster'] > 0) {
            $class = $this->database->getClasses()->byId($payload['cluster']);
            if ($payload['type'] === 'd') {
                $record = $this->database->createDocumentInstance($class);
            }
            else {
                $record = $this->database->createRecordInstance($class);
            }
            $record->setClass($class);
        }
        elseif ($payload['type'] === 'd') {
            $record = new Document($this->database);
        }
        else {
            $record = new Record($this->database);
        }
        $record->setId(new ID($payload['cluster'], $payload['position']));
        $record->setVersion(isset($payload['version']) ? $payload['version'] : 0);
        $record->setBytes($payload['bytes']);
        return $record;
    }

}
