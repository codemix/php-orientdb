<?php

namespace Orienta\Common;

interface MagicInterface {
    /**
     * Call a virtual property.
     *
     * @param string $name The name of the virtual property to call.
     * @param array $arguments The arguments to pass to the callee.
     *
     * @return mixed The result of the call.
     * @throws \BadMethodCallException If no such method can be found.
     */
    public function __call($name, $arguments);

    /**
     * Get a virtual property.
     *
     * @param string $name The name of the virtual property to get.
     *
     * @return mixed The value of the property.
     * @throws \OutOfBoundsException If no such property exists.
     */
    public function __get($name);

    /**
     * Set a virtual property value.
     *
     * @param string $name The name of the virtual property to set.
     * @param mixed $value The value of the virtual property.
     *
     * @throws \OutOfBoundsException If no such property exists.
     */
    public function __set($name, $value);

    /**
     * Determine whether a virtual property with the given name exists.
     *
     * @param string $name The name of the virtual property to check.
     *
     * @return bool The value of the property.
     */
    public function __isset($name);

    /**
     * Unset the virtual property with the given name.
     *
     * @param string $name The name of the virtual property to unset.
     */
    public function __unset($name);
}
