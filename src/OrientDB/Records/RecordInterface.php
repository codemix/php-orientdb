<?php

namespace OrientDB\Records;

use OrientDB\Classes\ClassInterface;
use OrientDB\Common\MagicInterface;

interface RecordInterface extends SerializableInterface, \JsonSerializable, MagicInterface
{
    /**
     * Gets the Record ID
     * @return \OrientDB\Records\ID
     */
    public function getId();

    /**
     * Sets the Id
     *
     * @param \OrientDB\Records\ID $id
     *
     * @return $this the current object
     */
    public function setId($id);


    /**
     * Sets the Class
     *
     * @param ClassInterface|string $class
     *
     * @return $this the current object
     */
    public function setClass($class);

    /**
     * Gets the Class
     * @return string
     */
    public function getClass();

    /**
     * Sets the Version
     *
     * @param int $version
     *
     * @return $this the current object
     */
    public function setVersion($version);

    /**
     * Gets the Version
     * @return int
     */
    public function getVersion();

    /**
     * Sets the Bytes
     *
     * @param string $bytes
     *
     * @return $this the current object
     */
    public function setBytes($bytes);

    /**
     * Gets the Bytes
     * @return string
     */
    public function getBytes();

    /**
     * Determine whether the record is new and yet to be saved.
     *
     * @return bool true if the record is new, otherwise false.
     */
    public function getIsNew();

    /**
     * Determine whether the record has been deleted from the database.
     *
     * @return boolean true if the record has been deleted.
     */
    public function getIsDeleted();

    /**
     * Save the record to the database.
     * @return $this The current record, saved.
     */
    public function save();


    /**
     * Deletes the record.
     *
     * @return $this The current record, now marked as deleted.
     * @throws \OrientDB\Exceptions\Exception If the record could not be deleted.
     */
    public function delete();


}
