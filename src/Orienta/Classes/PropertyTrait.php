<?php

namespace Orienta\Classes;

trait PropertyTrait
{
    /**
     * @var string The name of the property.
     */
    public $name;

    /**
     * @var int The property type.
     */
    public $type;

    /**
     * @var bool Whether the property is mandatory.
     */
    public $mandatory;

    /**
     * @var bool Whether the property is read only.
     */
    public $readonly;

    /**
     * @var bool Whether the property cannot contain null values.
     */
    public $notNull;

    /**
     * @var int The minimum property value.
     */
    public $min;

    /**
     * @var int The maximum property value.
     */
    public $max;

    /**
     * @var string The regular expression that the property value should match.
     */
    public $regexp;

    /**
     * @var string The collation for this property.
     */
    public $collate;

    /**
     * @var string The linked class for this property.
     */
    public $linkedClass;

    /**
     * @var array The custom fields for the property.
     */
    public $customFields = [];

    /**
     * @var ClassInterface The class this property belongs to.
     */
    protected $class;

    /**
     * @var array The data for the property.
     */
    protected $data = [];

    /**
     * Sets the Class
     *
     * @param \Orienta\Classes\ClassInterface $class
     *
     * @return $this the current object
     */
    public function setClass($class)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * Gets the Class
     * @return \Orienta\Classes\ClassInterface
     */
    public function getClass()
    {
        return $this->class;
    }

}
