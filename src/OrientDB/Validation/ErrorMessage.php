<?php

namespace OrientDB\Validation;

class ErrorMessage
{
    const __default = "The value is invalid.";

    const MANDATORY = "{property} is mandatory.";
    const NOT_NULL = "{property} cannot be null.";
    const READ_ONLY = "{property} is read-only.";
    const MIN_VALUE = "{property} must be at least {min}.";
    const MAX_VALUE = "{property} must be at most {max}.";
    const BAD_PATTERN = "{property} does not match the required pattern.";
    const BAD_TYPE = "{property} must be a {expected}, got {given}.";
}
