<?php

namespace Orienta\Protocols\Binary\Operations;

use Orienta\Exceptions\Exception;
use Orienta\Records\Deserializer;
use Orienta\Records\Document;
use Orienta\Records\ID;
use Orienta\Records\Record;

class RecordLoad extends AbstractDbOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 30;

    /**
     * @var int The id of the cluster for the record.
     */
    public $cluster;

    /**
     * @var int The position of the record in the cluster.
     */
    public $position;

    /**
     * @var string The fetch plan for the record.
     */
    public $fetchPlan = '';

    /**
     * @var bool Whether to ignore the cache, defaults to false.
     */
    public $ignoreCache = false;

    /**
     * @var bool Whether to load tombstones, defaults to false.
     */
    public $tombstones = false;

    /**
     * Write the data to the socket.
     */
    protected function write()
    {
        $this->writeShort($this->cluster);
        $this->writeLong($this->position);
        $this->writeString($this->fetchPlan);
        $this->writeBoolean($this->ignoreCache);
        $this->writeBoolean($this->tombstones);
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read()
    {
        $first = $this->readPayload();
        $children = [];
        while(($payload = $this->readPayload()) !== null) {
            $children[] = $payload;
        }
        // @todo assemble references
        return $first;
    }


    protected function readPayload()
    {
        $status = $this->readByte();
        $payload = [];
        switch ($status) {
            case 0:
                // no more content
                return null;
            case 1:
                // a normal record
                $payload['cluster'] = $this->cluster;
                $payload['position'] = $this->position;
                $payload['bytes'] = $this->readString();
                $payload['version'] = $this->readInt();
                $payload['type'] = $this->readChar();
                return $this->normalizePayload($payload);
            case 2:
                $payload['classId'] = $this->readShort();
                if ($payload['classId'] == -2 || $payload['classId'] == -3) {
                    return $this->normalizePayload($payload);
                }
                $payload['type'] = $this->readChar();
                $payload['bytes'] = $this->readShort();
                $payload['position'] = $this->readLong();
                $payload['version'] = $this->readInt();
                $payload['content'] = $this->readString();
                return $this->normalizePayload($payload);
            default:
                throw new Exception('Unknown payload status: "'.$status.'"');
        }
    }

    protected function normalizePayload(array $payload)
    {
        if (!isset($payload['type']))
            return $payload;

        if ($payload['type'] === 'd') {
            $record = new Document($this->database);
            $attributes = Deserializer::deserialize($payload['bytes']);
            if (isset($attributes['@class'])) {
                $record->setClass($attributes['@class']);
                unset($attributes['@class']);
            }
            $record->setAttributes($attributes);
        }
        else {
            $record = new Record($this->database);
            $record->setBytes($payload['bytes']);
        }
        $record->setId(new ID($payload['cluster'], $payload['position']));
        $record->setVersion(isset($payload['version']) ? $payload['version'] : 0);

        return $record;
    }
}
