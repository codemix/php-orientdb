<?php

namespace Orienta\Classes;

/**
 * @property string $name The name of the property.
 * @property int $type The property type.
 * @property bool $mandatory true if the property is mandatory.
 * @property bool $readonly true if the property is read only.
 * @property bool $notNull true if the property cannot contain null values.
 * @property int|null $min The minimum value, if any.
 * @property int|null $max The maximum value, if any.
 * @property string $regexp The regular expression for this property.
 * @property array $customFields The custom fields for the property.
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
     * Get an attribute with the given name.
     *
     * @param string $name The name of the attribute to get.
     *
     * @return mixed The value of the attribute.
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
     * Set an attribute with the given name.
     *
     * @param string $name The attribute name.
     * @param mixed $value The attribute value.
     */
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    /**
     * Determine whether the attribute with the given name exists.
     *
     * @param string $name The name of the attribute.
     *
     * @return bool true if the attribute exists
     */
    public function __isset($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * Unset the attribute with the given name.
     *
     * @param string $name The name of the attribute to unset.
     */
    public function __unset($name)
    {
        unset($this->data[$name]);
    }

}
