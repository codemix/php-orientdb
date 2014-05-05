<?php

namespace Oriento\Protocols\Binary\Operations;

class DbOpen extends AbstractOperation
{
    /**
     * @var int The op code.
     */
    public $opCode = 3;

    /**
     * @var string the name of the client library.
     */
    public $clientName = "Oriento PHP";

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
     * @var string the name of the database to open.
     */
    public $database;

    /**
     * @var string The type of database to open.
     */
    public $type = "graph";

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
        $this->writeString($this->database);
        $this->writeString($this->type);
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
        $sessionId = $this->readInt();
        $totalClusters = $this->readShort();
        $clusters = [];
        for ($i = 0; $i < $totalClusters; $i++) {
            $clusters[] = [
                'name' => $this->readString(),
                'id' => $this->readShort(),
                'type' => $this->readString(),
                'dataSegment' => $this->readShort()
            ];
        }
        return [
            'sessionId' => $sessionId,
            'clusters' => $clusters,
            'servers' => $this->readString(),
            'release' => $this->readString()

        ];
    }

}
