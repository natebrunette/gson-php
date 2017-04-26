<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Element;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use LogicException;
use stdClass;

/**
 * Class JsonObject
 *
 * Represents a json object
 *
 * @author Nate Brunette <n@tebru.net>
 */
class JsonObject extends JsonElement implements IteratorAggregate, Countable
{
    /**
     * Object properties
     *
     * @var JsonElement[]
     */
    private $properties = [];

    /**
     * Add a string to object at property
     *
     * @param string $property
     * @param string $value
     */
    public function addString($property, $value)
    {
        $this->add($property, JsonPrimitive::create($value));
    }

    /**
     * Add an integer to object at property
     *
     * @param string $property
     * @param int $value
     */
    public function addInteger($property, $value)
    {
        $this->add($property, JsonPrimitive::create($value));
    }

    /**
     * Add a float to object at property
     *
     * @param string $property
     * @param float $value
     */
    public function addFloat($property, $value)
    {
        $this->add($property, JsonPrimitive::create($value));
    }

    /**
     * Add a boolean to object at property
     *
     * @param string $property
     * @param bool $value
     */
    public function addBoolean($property, $value)
    {
        $this->add($property, JsonPrimitive::create($value));
    }

    /**
     * Add an element to object at property
     *
     * @param string $property
     * @param JsonElement $jsonElement
     */
    public function add($property, JsonElement $jsonElement)
    {
        $this->properties[$property] = $jsonElement;
    }

    /**
     * Returns true if the object has property
     *
     * @param string $property
     * @return bool
     */
    public function has($property)
    {
        return isset($this->properties[$property]);
    }

    /**
     * Get the value at property
     *
     * @param string $property
     * @return JsonElement
     */
    public function get($property)
    {
        return $this->properties[$property];
    }

    /**
     * Convenience method to get a value and ensure it's a primitive
     *
     * @param string $property
     * @return JsonPrimitive
     * @throws \LogicException
     */
    public function getAsJsonPrimitive($property)
    {
        /** @var JsonPrimitive $jsonElement */
        $jsonElement = $this->properties[$property];

        if (!$jsonElement->isJsonPrimitive()) {
            throw new LogicException('This value is not a primitive');
        }

        return $jsonElement;
    }

    /**
     * Convenience method to get a value and ensure it's an object
     *
     * @param string $property
     * @return JsonObject
     * @throws \LogicException
     */
    public function getAsJsonObject($property)
    {
        /** @var JsonObject $jsonElement */
        $jsonElement = $this->properties[$property];

        if (!$jsonElement->isJsonObject()) {
            throw new LogicException('This value is not an object');
        }

        return $jsonElement;
    }

    /**
     * Convenience method to get a value and ensure it's an array
     *
     * @param string $property
     * @return JsonArray
     * @throws \LogicException
     */
    public function getAsJsonArray($property)
    {
        /** @var JsonArray $jsonElement */
        $jsonElement = $this->properties[$property];

        if (!$jsonElement->isJsonArray()) {
            throw new LogicException('This value is not an array');
        }

        return $jsonElement;
    }

    /**
     * Get property as string
     *
     * @param string $property
     * @return string
     * @throws \LogicException
     */
    public function getAsString($property)
    {
        return $this->getAsJsonPrimitive($property)->asString();
    }

    /**
     * Get property as integer
     *
     * @param string $property
     * @return int
     * @throws \LogicException
     */
    public function getAsInteger($property)
    {
        return $this->getAsJsonPrimitive($property)->asInteger();
    }

    /**
     * Get property as float
     *
     * @param string $property
     * @return float
     * @throws \LogicException
     */
    public function getAsFloat($property)
    {
        return $this->getAsJsonPrimitive($property)->asFloat();
    }

    /**
     * Get property as boolean
     *
     * @param string $property
     * @return boolean
     * @throws \LogicException
     */
    public function getAsBoolean($property)
    {
        return $this->getAsJsonPrimitive($property)->asBoolean();
    }

    /**
     * Get property as array
     *
     * @param string $property
     * @return array
     */
    public function getAsArray($property)
    {
        return json_decode(json_encode($this->get($property)), true);
    }

    /**
     * Get the value as a JsonObject
     *
     * @return JsonObject
     */
    public function asJsonObject()
    {
        return $this;
    }

    /**
     * Remove property from object
     *
     * @param string $property
     * @return bool
     */
    public function remove($property)
    {
        if (!isset($this->properties[$property])) {
            return false;
        }

        unset($this->properties[$property]);

        return true;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return stdClass
     */
    public function jsonSerialize()
    {
        $class = new stdClass();
        foreach ($this->properties as $key => $property) {
            $class->$key = $property->jsonSerialize();
        }

        return $class;
    }

    /**
     * Retrieve an external iterator
     *
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->properties);
    }

    /**
     * Returns the number of properties in object
     *
     * @return int
     */
    public function count()
    {
        return count($this->properties);
    }
}
