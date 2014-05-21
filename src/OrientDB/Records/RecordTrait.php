<?php


namespace OrientDB\Records;

use OrientDB\Classes\ClassInterface;
use OrientDB\Common\MagicTrait;
use OrientDB\Databases\Database;
use OrientDB\Exceptions\Exception;

trait RecordTrait
{

    use MagicTrait;

    /**
     * @var ID The record id.
     */
    protected $id;

    /**
     * @var ClassInterface The class this record belongs to.
     */
    protected $class;

    /**
     * @var int The record version.
     */
    protected $version = 0;

    /**
     * @var string The raw bytes that make up the record.
     */
    protected $bytes;

    /**
     * @var Database The database the record belongs to.
     */
    protected $database;

    /**
     * @var bool Whether the record has been deleted.
     */
    protected $isDeleted = false;


    /**
     * Gets the Record ID
     * @return \OrientDB\Records\ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Id
     *
     * @param \OrientDB\Records\ID $id
     *
     * @return $this the current object
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }


    /**
     * Sets the Class
     *
     * @param ClassInterface|string $class
     *
     * @return $this the current object
     */
    public function setClass($class)
    {
        if (is_string($class)) {
            $class = $this->database->getClass($class);
        }
        $this->class = $class;
        return $this;
    }

    /**
     * Gets the Class
     * @return ClassInterface|null
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets the Version
     *
     * @param int $version
     *
     * @return $this the current object
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Gets the Version
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Sets the Bytes
     *
     * @param string $bytes
     *
     * @return $this the current object
     */
    public function setBytes($bytes)
    {
        $this->bytes = $bytes;
        return $this;
    }

    /**
     * Gets the Bytes
     * @return string
     */
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * Sets the Database
     *
     * @param \OrientDB\Databases\Database $database
     *
     * @return $this the current object
     */
    public function setDatabase($database)
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
     * Determine whether the record is new and yet to be saved.
     *
     * @return bool true if the record is new, otherwise false.
     */
    public function getIsNew()
    {
        return $this->id === null;
    }

    /**
     * Determine whether the record has been deleted from the database.
     *
     * @return boolean true if the record has been deleted.
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
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

        return $meta;
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

        return $meta;
    }

    /**
     * Save the record to the database.
     * @return $this The current record, saved.
     */
    public function save()
    {
        if ($this->getIsNew()) {
            $this->insert();
        }
        else {
            $this->update();
        }
        $this->isDeleted = false;
        return $this;
    }

    /**
     * Insert the record into the database.
     */
    protected function insert()
    {
        $cluster = isset($this->id) ? $this->id->cluster : $this->getClass()->defaultClusterId;

        $db = $this->getDatabase();
        $db->execute('recordCreate', [
            'record' => $this,
            'cluster' => $cluster
        ]);
    }

    /**
     * Update the existing record in the database.
     */
    protected function update()
    {
        $this->getDatabase()->execute('recordUpdate', [
            'record' => $this
        ]);
    }


    /**
     * Deletes the record.
     *
     * @return $this The current record, now marked as deleted.
     * @throws \OrientDB\Exceptions\Exception If the record could not be deleted.
     */
    public function delete()
    {
        if ($this->getIsNew()) {
            throw new Exception('Cannot delete an unsaved record.');
        }
        else if ($this->isDeleted) {
            throw new Exception('Cannot delete a record that has already been deleted.');
        }
        $result = $this->getDatabase()->delete($this->getId())->limit(1)->scalar();
        $this->isDeleted = (bool) $result;
        return $this;
    }

}

