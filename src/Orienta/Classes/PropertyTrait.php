<?php

namespace Orienta\Classes;

use Orienta\Validation\ErrorMessage;

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

    /**
     * Validate the given value.
     *
     * @param mixed $value The value to validate.
     *
     * @return array An array containing a boolean which is true if the value is valid,
     *                followed by an array of validation errors, if any.
     */
    public function validate($value)
    {
        if ($this->mandatory && ($value === null || $value === '')) {
            return [false, [$this->validationError(ErrorMessage::MANDATORY)]];
        }
        if ($this->notNull && $value === null) {
            return [false, [$this->validationError(ErrorMessage::NOT_NULL)]];
        }
        else if (!$this->notNull && $value === null) {
            return [true, []];
        }
        $errors = [];
        if ($this->regexp && !$this->validateRegExp($value)) {
            $errors[] = $this->validationError(ErrorMessage::BAD_PATTERN);
        }
        if ($this->min > 0 && !$this->validateMin($value)) {
            $errors[] = $this->validationError(ErrorMessage::MIN_VALUE, [
                '{min}' => $this->min
            ]);
        }
        if ($this->max > 0 && !$this->validateMax($value)) {
            $errors[] = $this->validationError(ErrorMessage::MAX_VALUE, [
                '{max}' => $this->max
            ]);
        }
        return [count($errors) === 0, $errors];
    }

    protected function validateType($value)
    {
        // @fixme implementation
        return true;
    }

    protected function validateMin($value)
    {
        // @todo type check?
        return $value >= $this->min;
    }

    protected function validateMax($value)
    {
        return $value <= $this->max;
    }

    protected function validateRegExp($value)
    {
        return preg_match('/'.$this->regexp.'/', $value);
    }

    /**
     * Return a validation error message.
     *
     * @param string $message The error message.
     * @param array $params The parameters for the message.
     *
     * @return string The processed message.
     */
    protected function validationError($message, array $params = [])
    {
        $params['{property}'] = $this->name;
        return strtr($message, $params);
    }

}
