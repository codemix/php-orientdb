<?php

namespace Orienta\Records;

trait DocumentTrait
{
    use RecordTrait;

    /**
     * @var array The record attributes.
     */
    protected $attributes;

    /**
     * Sets the attributes for the document.
     *
     * @param array $attributes The document attributes.
     *
     * @return $this the current object.
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Gets the attributes for the document.
     *
     * @return array The document attributes.
     */
    public function getAttributes()
    {
        if ($this->attributes === null) {
            $this->deserializeAttributes();
        }
        return $this->attributes;
    }

    /**
     * Deserialize the attributes for the document.
     */
    protected function deserializeAttributes()
    {
        if (!strlen($this->bytes)) {
            $this->attributes = [];
        }
        else {
            $this->attributes = Deserializer::deserialize($this->bytes);
        }
    }

    /**
     * Replace RIDs with their concrete instances.
     *
     * @param RecordInterface[] $instances The record / document instances.
     */
    public function resolveReferences($instances)
    {
        $attributes = $this->getAttributes();
        $this->setAttributes($this->resolveItemReferences($attributes, $instances));
    }

    /**
     * Replace RIDs with their concrete instances in the given subject.
     *
     * @param array $subject The array of fields containing references
     * @param RecordInterface[] $instances The instances
     *
     * @return array The array with references replaced
     */
    protected function resolveItemReferences(array $subject, $instances)
    {
        foreach($subject as $key => $value) {
            if (is_array($value)) {
                $subject[$key] = $this->resolveItemReferences($value, $instances);
            }
            else if ($value instanceof ID) {
                foreach($instances as $instance) {
                    $id = $instance->getId();
                    if ($value->cluster == $id->cluster && $value->position == $id->position) {
                        $subject[$key] = $instance;
                        break;
                    }
                }
            }
            else if ($value instanceof DocumentInterface) {
                $value->resolveReferences($instances);
            }
        }
        return $subject;
    }


    /**
     * Return a representation of the class that can be serialized as an
     * OrientDB record.
     *
     * @return mixed
     */
    public function recordSerialize()
    {
        $attributes = $this->getAttributes();
        if (($class = $this->getClass()) !== null) {
            $attributes['@class'] = $class->name;
        }

        return $attributes;
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
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     */
    public function count()
    {
        $attributes = $this->getAttributes();
        $count = count($attributes);
        return isset($attributes['@class']) ? $count - 1 : $count;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        if ($offset === '@class') {
            return false;
        }
        else {
            $attributes = $this->getAttributes();
            return isset($attributes[$offset]);
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        $attributes = $this->getAttributes();
        return isset($attributes[$offset]) ? $attributes[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     *
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->getAttributes();
        $this->attributes[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     *
     * @return void
     */
    public function offsetUnset($offset)
    {
        $this->getAttributes();
        unset($this->attributes[$offset]);
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
        $attributes = $this->getAttributes();
        if (array_key_exists($name, $attributes)) {
            return $attributes[$name];
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
        $this->getAttributes();
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
        $attributes = $this->getAttributes();
        return isset($attributes[$name]);
    }

    /**
     * Unset the virtual property with the given name.
     *
     * @param string $name The name of the virtual property to unset.
     */
    public function __unset($name)
    {
        $this->getAttributes();
        unset($this->attributes[$name]);
    }
}
