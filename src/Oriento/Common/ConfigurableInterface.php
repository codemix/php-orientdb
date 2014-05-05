<?php


namespace Oriento\Common;

interface ConfigurableInterface
{

    /**
     * Configure the object.
     *
     * @param array $options The options for the object.
     *
     * @return $this The current object, configured.
     */
    public function configure(array $options = array());
}
