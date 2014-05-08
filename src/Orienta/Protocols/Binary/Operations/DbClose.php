<?php

namespace Orienta\Protocols\Binary\Operations;

class DbClose extends AbstractOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 5;

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
     * @return boolean result
     */
    protected function read()
    {
        return true;
    }

}
