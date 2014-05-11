<?php

namespace Orienta\Classes;

use Orienta\Databases\Database;
use Orienta\Validation\ValidatorInterface;

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
     * @param \Orienta\Databases\Database $database
     *
     * @return $this the current object
     */
    public function setDatabase(Database $database);

    /**
     * Gets the Database
     * @return \Orienta\Databases\Database
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
     * @param \Orienta\Classes\PropertyList $properties
     *
     * @return $this the current object
     */
    public function setProperties($properties);

    /**
     * Gets the Properties
     * @return \Orienta\Classes\PropertyList
     */
    public function getProperties();

    /**
     * Create a document instance for this class.
     *
     * @param array $properties The properties for the instance.
     *
     * @return \Orienta\Records\DocumentInterface
     */
    public function createDocument(array $properties = []);
}
