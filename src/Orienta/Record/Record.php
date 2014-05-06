<?php

namespace Orienta\Record;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;
use Orienta\Common\MapTrait;
use Orienta\Database\Database;

class Record implements SerializableInterface, \JsonSerializable, ConfigurableInterface, MagicInterface
{
    use ConfigurableTrait;

    use MagicTrait {
        __call as __call;
    }

    /**
     * @var ID The record id.
     */
    protected $id;

    /**
     * @var Database The database the record belongs to.
     */
    protected $database;

    /**
     * @var array The record attributes.
     */
    protected $attributes = [];

    /**
     * # Constructor
     *
     * @param Database $database The database the record belongs to.
     * @param array $config The configuration for the record.
     */
    public function __construct(Database $database, array $config = [])
    {
        $this->database = $database;
    }


    /**
     * Gets the Record ID
     * @return \Orienta\Record\ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return a representation of the class that can be serialized as an
     * OrientDB record.
     *
     * @return mixed
     */
    public function recordSerialize()
    {
        $meta = [
            '@rid' => $this->id
        ];

        return array_merge($meta, $this->attributes);
    }

    /**
     * (PHP 5 &gt;= 5.4.0)<br/>
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $meta = [
            '@rid' => $this->id
        ];

        return array_merge($meta, $this->attributes);
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
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        }

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
        $this->attributes[$name] = $value;
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
        return isset($this->attributes[$name]);
    }

    /**
     * Unset the virtual property with the given name.
     *
     * @param string $name The name of the virtual property to unset.
     */
    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }


}
