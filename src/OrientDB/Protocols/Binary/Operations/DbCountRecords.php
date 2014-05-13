<?php

namespace OrientDB\Protocols\Binary\Operations;

class DbCountRecords extends AbstractDbOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 9;

    /**
     * @var int the sessionId for the db to be closed
     */
    public $sessionId = -1;

    /**
     * @var string The database storage type.
     */
    public $storage = 'plocal';

    /**
     * Write the data to the socket.
     */
    protected function write()
    {
    }

    /**
     * Read the response from the socket.
     *
     * @return int The record count
     */
    protected function read()
    {
        return $this->readLong();
    }

}