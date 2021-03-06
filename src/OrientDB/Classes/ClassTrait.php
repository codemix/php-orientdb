<?php

namespace OrientDB\Classes;

use OrientDB\Databases\Database;
use OrientDB\Records\DocumentInterface;
use OrientDB\Records\ID;
use OrientDB\Records\RecordInterface;
use OrientDB\Validation\ErrorMessage;

/**
 *
 * @property string $name
 * @property string $shortName
 * @property int $defaultClusterId
 * @property int[] $clusterIds
 * @property bool $abstract
 *
 * @property PropertyList $properties
 *
 * @package OrientDB\Classes
 */
trait ClassTrait
{

    /**
     * @var PropertyInterface[]
     */
    protected $properties;

    /**
     * @var array The data for the class.
     */
    protected $data = [];

    /**
     * @var Database The database the class belongs to.
     */
    protected $database;

    /**
     * Sets the Database
     *
     * @param \OrientDB\Databases\Database $database
     *
     * @return $this the current object
     */
    public function setDatabase(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * Gets the Database
     * @return \OrientDB\Databases\Database
     */
    public function getDatabase()
    {
        return $this->database;
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
     * Sets the Properties
     *
     * @param \OrientDB\Classes\PropertyInterface[] $properties
     *
     * @return $this the current object
     */
    public function setProperties($properties)
    {
        if (is_array($properties)) {
            $properties = new PropertyList($this, $properties);
        }
        $this->properties = $properties;
        return $this;
    }

    /**
     * Gets the Properties
     * @return \OrientDB\Classes\PropertyInterface[]
     */
    public function getProperties()
    {
        if ($this->properties === null) {
            $this->properties = new PropertyList($this, $this->data['properties']);
        }
        return $this->properties;
    }


    /**
     * Get an attribute with the given name.
     *
     * @param string $name The name of the attribute to get.
     * @param null $default The default value to return if no such attribute is specified.
     *
     * @return mixed The attribute value.
     */
    public function getAttribute($name, $default = null)
    {
        return isset($this->data[$name]) ? $this->data[$name] : $default;
    }

    /**
     * Set an attribute value.
     *
     * @param string $name The name of the attribute to set.
     * @param mixed $value The value of the attribute.
     *
     * @return $this The current object.
     */
    public function setAttribute($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }


    /**
     * Validate the given value.
     *
     * @param mixed $value The value to validate.
     *
     * @return array An array containing a boolean which is true if the value is valid,
     *                followed by an array of validation errors, if any.
     */
    public function validate($value)
    {
        $allErrors = [];
        foreach($this->getProperties() as $name => $property /* @var Property $property */) {
            if ($property->readonly && $value instanceof DocumentInterface && !$value->getIsNew() && isset($value[$name])) {
                $allErrors[] = $this->validationError(ErrorMessage::READ_ONLY, ['{property}' => $name]);
            }
            else {
                list($isValid, $errors) = $property->validate(isset($value[$name]) ? $value[$name] : null);
                if (!$isValid) {
                    $allErrors = array_merge($allErrors, $errors);
                }
            }
        }
        return [count($allErrors) === 0, $allErrors];
    }

    /**
     * Return a validation error message.
     *
     * @param string $message The error message.
     * @param array $params The parameters for the message.
     *
     * @return string The processed message.
     */
    protected function validationError($message, array $params = [])
    {
        $params['{class}'] = $this->name;
        return strtr($message, $params);
    }

    /**
     * Create a document instance for this class.
     *
     * @param array $properties The properties for the instance.
     *
     * @return \OrientDB\Records\DocumentInterface
     */
    public function createDocument(array $properties = [])
    {
        return $this->getDatabase()->createDocumentInstance($this, [
            'class' => $this,
            'attributes' => $properties
        ]);
    }


    /**
     * Load a record for this class.
     *
     * @param mixed $id The record id or position.
     * @param array $options The options for the command.
     *
     * @return RecordInterface|DocumentInterface|null The loaded record instance, or null if the record doesn't exist.
     */
    public function load($id, array $options = [])
    {
        if (is_numeric($id)) {
            $options['cluster'] = $this->defaultClusterId;
            $options['position'] = $id;
        }
        else {
            if (!($id instanceof ID)) {
                $id = new ID($id);
            }
            $options['cluster'] = $id->cluster === null ? $this->defaultClusterId : $id->cluster;
            $options['position'] = $id->position;
        }

        return $this->getDatabase()->execute('recordLoad', $options);
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
        if ($name === 'properties') {
            return $this->getProperties();
        }
        else if (array_key_exists($name, $this->data)) {
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
        if ($name === 'properties') {
            $this->setProperties($value);
        }
        else {
            $this->data[$name] = $value;
        }
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
        if ($name === 'properties') {
            return true;
        }
        else {
            return isset($this->data[$name]);
        }
    }

    /**
     * Unset the attribute with the given name.
     *
     * @param string $name The name of the attribute to unset.
     */
    public function __unset($name)
    {
        if ($name === 'properties') {
            $this->properties = null;
        }
        else {
            unset($this->data[$name]);
        }
    }
}
