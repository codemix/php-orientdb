<?php

namespace Orienta\Common;


trait MapTrait
{
    use ListTrait;

    /**
     * Call a virtual property.
     *
     * @param string $name The name of the virtual property to call.
     * @param array $arguments The arguments to pass to the callee.
     *
     * @return mixed The result of the call.
     * @throws \BadMethodCallException If no such method can be found.
     */
    public function __call($name, $arguments)
    {
        if (isset($this->items[$name]) && is_callable($this->items[$name])) {
            return call_user_func_array($this->items[$name], $arguments);
        }
        else {
            throw new \BadMethodCallException(get_called_class().' does not have a method called "'.$name.'"');
        }
    }

    /**
     * Get a virtual property.
     *
     * @param string $name The name of the virtual property to get.
     *
     * @return mixed The value of the property.
     * @throws \OutOfBoundsException If no such property exists.
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->items)) {
            return $this->items[$name];
        }
        else {
            throw new \OutOfBoundsException(get_called_class().' does not have a property called "'.$name.'"');
        }
    }

    /**
     * Set a virtual property value.
     *
     * @param string $name The name of the virtual property to set.
     * @param mixed $value The value of the virtual property.
     *
     * @throws \OutOfBoundsException If no such property exists.
     */
    public function __set($name, $value)
    {
        $this->items[$name] = $value;
    }

    /**
     * Determine whether a virtual property with the given name exists.
     *
     * @param string $name The name of the virtual property to check.
     *
     * @return bool The value of the property.
     */
    public function __isset($name)
    {
        return isset($this->items[$name]);
    }

    /**
     * Unset the virtual property with the given name.
     *
     * @param string $name The name of the virtual property to unset.
     */
    public function __unset($name)
    {
        unset($this->items[$name]);
    }

}
