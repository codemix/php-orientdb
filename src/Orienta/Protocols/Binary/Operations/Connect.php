<?php

namespace Orienta\Protocols\Binary\Operations;

class Connect extends AbstractOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 2;

    /**
     * @var string the name of the client library.
     */
    public $clientName = "Orienta PHP";

    /**
     * @var string the client version.
     * @todo extract me from composer.json
     */
    public $clientVersion = "0.0.1";

    /**
     * @var int the maximum known protocol version
     */
    public $protocolVersion = 20;

    /**
     * @var string the username to connect with.
     */
    public $username;

    /**
     * @var string the password to connect with.
     */
    public $password;

    /**
     * Write the data to the socket.
     */
    protected function write()
    {
        $this->writeString($this->clientName);
        $this->writeString($this->clientVersion);
        $this->writeShort($this->protocolVersion);
        $this->writeString(''); // client id, unused.
        $this->writeString($this->username);
        $this->writeString($this->password);
    }

    /**
     * Read the response from the socket.
     *
     * @return int The session id.
     */
    protected function read()
    {
        return $this->readInt();
    }

}
