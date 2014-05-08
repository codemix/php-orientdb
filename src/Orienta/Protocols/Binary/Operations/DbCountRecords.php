<?php

namespace Orienta\Protocols\Binary\Operations;

class DbCountRecords extends AbstractOperation
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
     * Write the data to the socket.
     */
    protected function write()
    {
        //nothing else to write but the header
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
