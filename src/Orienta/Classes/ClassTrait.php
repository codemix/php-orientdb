<?php

namespace Orienta\Classes;

use Orienta\Databases\Database;

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
     * @param \Orienta\Databases\Database $database
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
     * @return \Orienta\Databases\Database
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
     * @param \Orienta\Classes\PropertyInterface[] $properties
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
     * @return \Orienta\Classes\PropertyInterface[]
     */
    public function getProperties()
    {
        if ($this->properties === null) {
            $this->properties = new PropertyList($this, $this->data['properties']);
        }
        return $this->properties;
    }

}
