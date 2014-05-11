<?php

namespace Orienta\Validation;

interface ValidatorInterface
{
    /**
     * Validate the given value.
     *
     * @param mixed $value The value to validate.
     *
     * @return array An array containing a boolean which is true if the value is valid,
     *                followed by an array of validation errors, if any.
     */
    public function validate($value);
}
