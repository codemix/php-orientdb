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

class RecordUpdate extends AbstractDbOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 32;

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
        $id = $this->record->getId();
        $this->writeShort($id->cluster);
        $this->writeLong($id->position);
        $this->writeBytes(Serializer::serialize($this->record));
        $this->writeInt($this->record->getVersion());
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
        $this->record->setVersion($this->readInt());
        $totalChanges = $this->readInt();
        return $this->record;
    }



}
