<?php

namespace Orienta\Protocols\Common;

use Orienta\Common\ConfigurableInterface;

interface TransportInterface extends ConfigurableInterface
{

    /**
     * Execute the operation with the given name.
     *
     * @param string $operation The operation to execute.
     * @param array $params The parameters for the operation.
     *
     * @return mixed The result of the operation.
     */
    public function execute($operation, array $params = array());

}
