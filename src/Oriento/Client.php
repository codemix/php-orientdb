<?php

namespace Oriento;

use Oriento\Common\ConfigurableInterface;
use Oriento\Common\ConfigurableTrait;
use Oriento\Protocols\Common\TransportInterface;

class Client implements ConfigurableInterface
{
    use ConfigurableTrait;

    const BINARY_TRANSPORT = 'Oriento\\Protocols\\Binary\\Transport';

    /**
     * @var string The server hostname.
     */
    public $hostname = 'localhost';

    /**
     * @var string The port for the server.
     */
    public $port;

    /**
     * @var string The username for the server.
     */
    public $username = 'root';

    /**
     * @var string The password for the server.
     */
    public $password = 'root';

    /**
     * @var TransportInterface The transport to use for the connection to the server.
     */
    protected $_transport;

    /**
     * Sets the transport
     *
     * @param \Oriento\Protocols\Common\TransportInterface $transport
     *
     * @return $this the current object
     */
    public function setTransport($transport)
    {
        $this->_transport = $this->createTransport($transport);
        return $this;
    }

    /**
     * Gets the transport
     *
     * @return \Oriento\Protocols\Common\TransportInterface
     */
    public function getTransport()
    {
        if ($this->_transport === null) {
            $this->createTransport();
        }
        return $this->_transport;
    }

    /**
     * Create a transport instance.
     *
     * @param TransportInterface|array|null $transport
     *
     * @return Protocols\Binary\Transport the transport interface
     */
    protected function createTransport($transport = null)
    {
        if (is_string($transport)) {
            if ($transport === 'binary') {
                $transport = new Protocols\Binary\Transport();
            }
            else {
                $transport = new $transport();
            }
        }
        else if (is_array($transport)) {
            if (isset($transport['class'])) {
                $className = $transport['class'];
                unset($transport['class']);
            }
            else {
                $className = 'Oriento\\Protocols\\Binary\\Transport';
            }
            $transport = new $className();
        }
        else if (!($transport instanceof TransportInterface)) {
            $transport = new Protocols\Binary\Transport();
        }
        $transport->configure([
            'hostname' => $this->hostname,
            'port' => $this->port,
            'username' => $this->username,
            'password' => $this->password
        ]);
        return $transport;
    }


}
