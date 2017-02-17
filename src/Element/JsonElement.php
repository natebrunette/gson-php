<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Element;

use JsonSerializable;
use Tebru\Gson\Exception\UnsupportedMethodException;

/**
 * Class JsonElement
 *
 * Base class for json element types
 *
 * @author Nate Brunette <n@tebru.net>
 */
abstract class JsonElement implements JsonSerializable
{
    /**
     * Returns from if the element is an instance of [@see JsonObject]
     *
     * @return bool
     */
    public function isJsonObject()
    {
        return $this instanceof JsonObject;
    }

    /**
     * Returns from if the element is an instance of [@see JsonArray]
     *
     * @return bool
     */
    public function isJsonArray()
    {
        return $this instanceof JsonArray;
    }

    /**
     * Returns from if the element is an instance of [@see JsonPrimitive]
     *
     * @return bool
     */
    public function isJsonPrimitive()
    {
        return $this instanceof JsonPrimitive;
    }

    /**
     * Returns from if the element is an instance of [@see JsonNull]
     *
     * @return bool
     */
    public function isJsonNull()
    {
        return $this instanceof JsonNull;
    }

    /**
     * Returns true if the value is a string
     *
     * @return bool
     */
    public function isString()
    {
        return false;
    }

    /**
     * Returns true if the value is an integer
     *
     * @return bool
     */
    public function isInteger()
    {
        return false;
    }

    /**
     * Returns true if the value is a float
     *
     * @return bool
     */
    public function isFloat()
    {
        return false;
    }

    /**
     * Returns true if the value is an integer or float
     *
     * @return bool
     */
    public function isNumber()
    {
        return false;
    }

    /**
     * Returns true if the value is a boolean
     *
     * @return bool
     */
    public function isBoolean()
    {
        return false;
    }

    /**
     * Cast the value to a string
     *
     * @return string
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asString()
    {
        throw new UnsupportedMethodException(sprintf('This method "asString" is not supported on "%s"', get_called_class()));
    }

    /**
     * Cast the value to an integer
     *
     * @return int
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asInteger()
    {
        throw new UnsupportedMethodException(sprintf('This method "asInteger" is not supported on "%s"', get_called_class()));
    }

    /**
     * Cast the value to a float
     *
     * @return float
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asFloat()
    {
        throw new UnsupportedMethodException(sprintf('This method "asFloat" is not supported on "%s"', get_called_class()));
    }

    /**
     * Cast the value to a boolean
     *
     * @return bool
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asBoolean()
    {
        throw new UnsupportedMethodException(sprintf('This method "asBoolean" is not supported on "%s"', get_called_class()));
    }

    /**
     * Get the value as a JsonObject
     *
     * @return JsonObject
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asJsonObject()
    {
        throw new UnsupportedMethodException(sprintf('This method "asJsonObject" is not supported on "%s"', get_called_class()));
    }

    /**
     * Get the value as a JsonArray
     *
     * @return JsonArray
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function asJsonArray()
    {
        throw new UnsupportedMethodException(sprintf('This method "asJsonArray" is not supported on "%s"', get_called_class()));
    }

    /**
     * Get the current value
     *
     * @return mixed
     * @throws \Tebru\Gson\Exception\UnsupportedMethodException
     */
    public function getValue()
    {
        throw new UnsupportedMethodException(sprintf('This method "getValue" is not supported on "%s"', get_called_class()));
    }
}
