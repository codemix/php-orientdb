<?php

namespace OrientDB\Protocols\Binary\Operations;

use OrientDB\Exceptions\Exception;
use OrientDB\Records\Deserializer;
use OrientDB\Records\Document;
use OrientDB\Records\DocumentInterface;
use OrientDB\Records\ID;
use OrientDB\Records\Record;
use OrientDB\Records\RecordInterface;
use OrientDB\Records\Serializer;

class RecordCreate extends AbstractDbOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 31;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster;

    /**
     * @var int The data segment for the record.
     */
    public $segment = -1;

    /**
     * @var RecordInterface The record to add.
     */
    public $record;

    /**
     * @var int The operation mode.
     */
    public $mode = 0;

    /**
     * Write the data to the socket.
     */
    protected function write()
    {
        $this->writeInt($this->segment);
        $this->writeShort($this->cluster);
        $this->writeBytes(Serializer::serialize($this->record));

        // record type
        if ($this->record instanceof DocumentInterface) {
            $this->writeChar('d');
        }
        else {
            $this->writeChar('b'); // @todo determine from record
        }

        $this->writeByte($this->mode);
    }

    /**
     * Read the response from the socket.
     *
     * @return RecordInterface The record instance, with RID
     */
    protected function read()
    {
        $this->record->setId(new ID($this->cluster, $this->readLong()));
        $this->record->setVersion($this->readInt());
        $totalChanges = $this->readInt();

        return $this->record;
    }



}
