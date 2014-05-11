<?php

namespace Orienta\Classes;

use Orienta\Common\ConfigurableInterface;
use Orienta\Common\ConfigurableTrait;
use Orienta\Common\MagicInterface;
use Orienta\Common\MagicTrait;

class Property implements PropertyInterface, ConfigurableInterface, MagicInterface
{
    use PropertyTrait;
    use MagicTrait;
    use ConfigurableTrait;
}
