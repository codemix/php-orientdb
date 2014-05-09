<?php

namespace Orienta\Classes;

class BuiltinClass implements ClassInterface
{
    use ClassTrait;

    /**
     * @var string[] The names of the known builtin classes
     */
    public static $classNames = [
        'OUser',
        'OFunction',
        'ORIDs',
        'ORestricted',
        'ORole',
        'OSchedule',
        'OTriggered',
        'V',
        'E'
    ];
}
