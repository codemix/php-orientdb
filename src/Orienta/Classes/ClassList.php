<?php

namespace Orienta\Classes;


use Orienta\Common\MapInterface;
use Orienta\Common\MapTrait;
use Orienta\Databases\Database;

class ClassList implements MapInterface
{
    use MapTrait;

    /**
     * @var Database The database the list of clusters is for.
     */
    protected $database;

    /**
     * @param Database $database The database the list of clusters is for.
     * @param array $items The items in the list
     */
    public function __construct($database, array $items = [])
    {
        $this->database = $database;
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
        else if ($value instanceof ClassInterface) {
            $offset = $value->getName();
        }
        $this->items[$offset] = $value;
    }


    /**
     * @inheritDoc
     *
     * @param string $offset The name of the class to get.
     *
     * @return null|BuiltinClass|CustomClass The class instance.
     */
    public function offsetGet($offset)
    {
        if (!isset($this->items[$offset])) {
            return null;
        }
        $value = $this->items[$offset];
        if (is_array($value)) {
            $value = $this->instantiateClass($value);
            $this->items[$offset] = $value;
        }
        return $value;
    }

    /**
     * Instantiate a class instance based on the given configuration.
     *
     * @param array $config The configuration array
     *
     * @return BuiltinClass|CustomClass
     */
    protected function instantiateClass(array $config)
    {
        if (in_array($config['name'], BuiltinClass::$classNames, true)) {
            $class = new BuiltinClass();
        }
        else {
            $class = new CustomClass();
        }

        $class->setDatabase($this->database);
        $class->setData($config);
        return $class;
    }

}
