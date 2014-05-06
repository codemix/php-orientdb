<?php

namespace Orienta\Common;


trait MagicTrait
{
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
        $getter = 'get'.$name;
        if (property_exists($this, $name) && ($var = $this->{$name}) && is_callable($var)) {
            return call_user_func_array($var, $arguments);
        }
        else if (method_exists($this, $getter) && ($var = $this->{$getter}()) && is_callable($var)) {
            return call_user_func_array($var, $arguments);
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
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            return $this->{$getter}();
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
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->{$setter}($value);
        }
        else {
            $getter = 'get'.$name;
            if (method_exists($this, $getter)) {
                throw new \OutOfBoundsException(get_called_class().'.'.$name.' is read only.');
            }
            else {
                throw new \OutOfBoundsException(get_called_class().' does not have a property called "'.$name.'"');
            }
        }
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
        $getter = 'get'.$name;
        if (method_exists($this, $getter)) {
            $result = $this->{$getter}();
            return $result !== null;
        }
        else {
            return false;
        }
    }

    /**
     * Unset the virtual property with the given name.
     *
     * @param string $name The name of the virtual property to unset.
     */
    public function __unset($name)
    {
        $setter = 'set'.$name;
        if (method_exists($this, $setter)) {
            $this->{$setter}(null);
        }
    }

}
