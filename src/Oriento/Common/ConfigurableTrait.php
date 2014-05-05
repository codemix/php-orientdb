<?php

namespace Oriento\Common;

trait ConfigurableTrait
{
    /**
     * Configure the object.
     *
     * @param array $options The options for the object.
     *
     * @return $this The current object, configured.
     */
    public function configure(array $options = array())
    {
        foreach($options as $key => $value) {
            $methodName = 'set'.$key;
            if (method_exists($this, $methodName)) {
                $this->{$methodName}($value);
            }
            else {
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * Return a new class instance configured from the given options.
     *
     * @param array $options The options for the newly created class instance.
     *
     * @return static The configured object.
     */
    public static function fromConfig (array $options = array())
    {
        $className = get_called_class();
        $object = new $className(); /* @var ConfigurableInterface $object */
        return $object->configure($options);
    }
}
