<?php

namespace Orienta\Record;

use Orienta\Common\MagicInterface;

interface RecordInterface extends SerializableInterface, \JsonSerializable, MagicInterface
{
    /**
     * Gets the Record ID
     * @return \Orienta\Record\ID
     */
    public function getId();

    /**
     * Sets the Id
     *
     * @param \Orienta\Record\ID $id
     *
     * @return $this the current object
     */
    public function setId($id);


    /**
     * Sets the Class
     *
     * @param string $class
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
}
