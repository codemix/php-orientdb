<?php

namespace Orienta\Classes;

/**
 *
 *
 * @property string $name The name of the property.
 * @property int $type The property type
 *
 * @package Orienta\Classes
 */
trait PropertyTrait
{

    /**
     * @var ClassInterface The class this property belongs to.
     */
    protected $class;

    /**
     * @var array The data for the property.
     */
    protected $data = [];

    /**
     * Sets the Class
     *
     * @param \Orienta\Classes\ClassInterface $class
     *
     * @return $this the current object
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Gets the Class
     * @return \Orienta\Classes\ClassInterface
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the Data
     *
     * @param array $data
     *
     * @return $this the current object
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Gets the Data
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get a property with the given name.
     *
     * @param string $name The name of the property to get.
     *
     * @return mixed The value of the property.
     * @throws \OutOfBoundsException
     */
    public function __get($name)
    {
        if (array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
        else {
            throw new \OutOfBoundsException(get_called_class().' does not have a property called "'.$name.'"');
        }
    }

    /**
     * Set a property with the given name.
     *
     * @param string $name The property name.
     * @param mixed $value The property value.
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Determine whether the property with the given name exists.
     *
     * @param string $name The name of the property.
     *
     * @return bool true if the property exists
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset the property with the given name.
     *
     * @param string $name The name of the property to unset.
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }


}
