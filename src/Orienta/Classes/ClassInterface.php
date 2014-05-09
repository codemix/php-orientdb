<?php

namespace Orienta\Classes;

use Orienta\Databases\Database;

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
interface ClassInterface
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
}
