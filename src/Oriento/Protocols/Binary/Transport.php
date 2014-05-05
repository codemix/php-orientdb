<?php

namespace Oriento\Protocols\Binary;

use Oriento\Protocols\Binary\Operations\AbstractOperation;
use Oriento\Protocols\Common\AbstractTransport;

class Transport extends AbstractTransport
{
    /**
     * @var Socket the connected socket.
     */
    protected $socket;

    /**
     * Gets the Socket, and establishes the connection if required.
     *
     * @return \Oriento\Protocols\Binary\Socket
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
        return $this->createOperation($operation, $params)->execute();
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
                $operation = 'Oriento\\Protocols\\Binary\\Operations\\'.ucfirst($operation);
            }
            $operation = new $operation();
        }
        $operation->socket = $this->getSocket();
        $operation->configure($params);
        return $operation;
    }

}
