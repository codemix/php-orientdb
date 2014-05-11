<?php

namespace Orienta\Classes;


use Orienta\Common\MapInterface;
use Orienta\Common\MapTrait;
use Orienta\Databases\Database;

class PropertyList implements MapInterface
{
    use MapTrait;

    /**
     * @var ClassInterface The class that these properties belong to.
     */
    protected $class;

    /**
     * @param ClassInterface $class The class the properties are for.
     * @param array $items The items in the list
     */
    public function __construct($class, array $items = [])
    {
        $this->class = $class;
        foreach($items as $key => $value) {
            $this->offsetSet($key, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        if (is_array($value) && isset($value['name'])) {
            $offset = $value['name'];
        }
        else if ($value instanceof Property) {
            $offset = $value->name;
        }
        $this->items[$offset] = $value;
    }


    /**
     * @inheritDoc
     *
     * @param string $offset The name of the property to get.
     *
     * @return null|PropertyInterface The property instance.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->items[$offset])) {
            return null;
        }
        $value = $this->items[$offset];
        if (is_array($value)) {
            $value = $this->instantiateProperty($value);
            $this->items[$offset] = $value;
        }
        return $value;
    }

    /**
     * Instantiate a property instance based on the given configuration.
     *
     * @param array $config The configuration array
     *
     * @return PropertyInterface
     */
    protected function instantiateProperty(array $config)
    {
        $property = Property::fromConfig($config);
        $property->setClass($this->class);

        return $property;
    }

}
