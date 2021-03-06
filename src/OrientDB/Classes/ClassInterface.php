<?php

namespace OrientDB\Classes;

use OrientDB\Databases\Database;
use OrientDB\Records\DocumentInterface;
use OrientDB\Records\RecordInterface;
use OrientDB\Validation\ValidatorInterface;

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
 */
interface ClassInterface extends ValidatorInterface
{
    /**
     * Sets the Database
     *
     * @param \OrientDB\Databases\Database $database
     *
     * @return $this the current object
     */
    public function setDatabase(Database $database);

    /**
     * Gets the Database
     * @return \OrientDB\Databases\Database
     */
    public function getDatabase();

    /**
     * Sets the Data
     *
     * @param array $data
     *
     * @return $this the current object
     */
    public function setData($data);

    /**
     * Gets the Data
     * @return array
     */
    public function getData();

    /**
     * Sets the Properties
     *
     * @param \OrientDB\Classes\PropertyList $properties
     *
     * @return $this the current object
     */
    public function setProperties($properties);

    /**
     * Gets the Properties
     * @return \OrientDB\Classes\PropertyList
     */
    public function getProperties();

    /**
     * Create a document instance for this class.
     *
     * @param array $properties The properties for the instance.
     *
     * @return \OrientDB\Records\DocumentInterface
     */
    public function createDocument(array $properties = []);

    /**
     * Load a record for this class.
     *
     * @param mixed $id The record id or position.
     * @param array $options The options for the command.
     *
     * @return RecordInterface|DocumentInterface|null The loaded record instance, or null if the record doesn't exist.
     */
    public function load($id, array $options = []);
}
