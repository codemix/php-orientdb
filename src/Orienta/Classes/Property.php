<?php

namespace Orienta\Classes;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;
use Orienta\Validation\ValidatorInterface;

class Property implements PropertyInterface, ConfigurableInterface, MagicInterface, ValidatorInterface
{
    use PropertyTrait;
    use MagicTrait;
    use ConfigurableTrait;

    /**
     * @param ClassInterface $class The class for the property.
     * @param array $config The configuration for the property.
     */
    public function __construct(ClassInterface $class = null, array $config = null)
    {
        $this->class = $class;
        if ($config !== null) {
            $this->configure($config);
        }
    }
}
