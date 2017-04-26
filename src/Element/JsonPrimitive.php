<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Element;

/**
 * Class JsonPrimitive
 *
 * Represents a json primitive (number, string, boolean)
 *
 * @author Nate Brunette <n@tebru.net>
 */
class JsonPrimitive extends JsonElement
{
    /**
     * The value of the primitive
     *
     * @var mixed
     */
    private $value;

    /**
     * Constructor
     *
     * @param mixed $value
     */
    protected function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Factory constructor that handles nulls
     *
     * @param mixed $value
     * @return JsonNull|JsonPrimitive
     */
    public static function create($value)
    {
        if (null === $value) {
            return new JsonNull();
        }

        return new self($value);
    }

    /**
     * Returns true if the value is a string
     *
     * @return bool
     */
    public function isString()
    {
        return is_string($this->value);
    }

    /**
     * Returns true if the value is an integer
     *
     * @return bool
     */
    public function isInteger()
    {
        return is_int($this->value);
    }

    /**
     * Returns true if the value is a float
     *
     * @return bool
     */
    public function isFloat()
    {
        return is_float($this->value);
    }

    /**
     * Returns true if the value is an integer or float
     *
     * @return bool
     */
    public function isNumber()
    {
        return $this->isInteger() || $this->isFloat();
    }

    /**
     * Returns true if the value is a boolean
     *
     * @return bool
     */
    public function isBoolean()
    {
        return is_bool($this->value);
    }

    /**
     * Cast the value to a string
     *
     * @return string
     */
    public function asString()
    {
        return (string) $this->value;
    }

    /**
     * Cast the value to an integer
     *
     * @return int
     */
    public function asInteger()
    {
        return (int) $this->value;
    }

    /**
     * Cast the value to a float
     *
     * @return float
     */
    public function asFloat()
    {
        return (float) $this->value;
    }

    /**
     * Cast the value to a boolean
     *
     * @return bool
     */
    public function asBoolean()
    {
        return (bool) $this->value;
    }

    /**
     * Return whatever the current value is
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->value;
    }
}
