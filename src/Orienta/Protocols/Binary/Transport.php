<?php

namespace Orienta\Protocols\Binary;

use Orienta\Protocols\Binary\Operations\AbstractOperation;
use Orienta\Protocols\Binary\Operations\Connect;
use Orienta\Protocols\Binary\Operations\DbExists;
use Orienta\Protocols\Binary\Operations\DbList;
use Orienta\Protocols\Binary\Operations\DbOpen;
use Orienta\Protocols\Common\AbstractTransport;

class Transport extends AbstractTransport
{
    /**
     * @var Socket the connected socket.
     */
    protected $socket;

    /**
     * @var int The session id for the connection.
     */
    protected $sessionId;

    /**
     * Gets the Socket, and establishes the connection if required.
     *
     * @return \Orienta\Protocols\Binary\Socket
     */
    public function getSocket()
    {
        if ($this->socket === null) {
            $this->socket = new Socket($this->hostname, $this->port);
        }
        return $this->socket;
    }


    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to execute.
     * @param array $params The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute($operation, array $params = array())
    {
        $op = $this->createOperation($operation, $params);
        if ((!isset($params['sessionId']) || $params['sessionId'] === -1)
            && !($op instanceof DbOpen)
            && !($op instanceof Connect)
        ) {
            if (!isset($this->sessionId)) {
                $this->authenticate();
            }
            $params['sessionId'] = $this->sessionId;
            $op->sessionId = $this->sessionId;
        }
        return $op->execute();
    }

    protected function authenticate()
    {
        $op = $this->createOperation('connect', [
            'username' => $this->username,
            'password' => $this->password
        ]);
        $this->sessionId = $op->execute();
    }

    /**
     * @param AbstractOperation|string $operation The operation name or instance.
     * @param array $params The parameters for the operation.
     *
     * @return AbstractOperation The operation instance.
     */
    protected function createOperation($operation, array $params)
    {
        if (!($operation instanceof AbstractOperation)) {
            if (!strstr($operation, '\\')) {
                $operation = 'Orienta\Protocols\Binary\Operations\\'.ucfirst($operation);
            }
            $operation = new $operation();
        }
        $operation->socket = $this->getSocket();
        $operation->configure($params);
        return $operation;
    }

}
