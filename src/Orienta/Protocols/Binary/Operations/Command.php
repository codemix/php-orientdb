<?php

namespace Orienta\Protocols\Binary\Operations;

use Orienta\Exceptions\Exception;
use Orienta\Query\QueryInterface;

class Command extends AbstractDbOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 41;

    /**
     * @var string The query mode.
     */
    public $mode = 's';

    /**
     * @var QueryInterface The query object.
     */
    public $query;


    /**
     * Write the data to the socket.
     */
    protected function write()
    {
        $this->writeChar($this->mode);
        $this->writeBytes($this->query->binarySerialize());
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read()
    {
        $results = [];
        while(($payload = $this->readPayload()) !== null) {
            $results[] = $payload;
        }
        return $results;
    }

    protected function readPayload()
    {
        $first = $this->readByte();
        switch($first) {
            case 0:
                // end of results
                return null;
            case 110;
                // null record
                return [
                    'type' => 'r',
                    'content' => null
                ];
            case 1:
            case 114:
                // a record
                return [
                    'type' => 'r',
                    'content' => $this->readRecord()
                ];
            case 2:
                // a prefetched record
                return [
                    'type' => 'p',
                    'content' => $this->readRecord()
                ];
            case 97:
                // a serialized result
                return [
                    'type' => 'f',
                    'content' => $this->readString()
                ];
            case 108:
                // a collection of records
                return [
                    'type' => 'l',
                    'content' => $this->readCollection()
                ];
            default:
                throw new Exception('Unknown payload type: '.$first);
        }
    }


}
