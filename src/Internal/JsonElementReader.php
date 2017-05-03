<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use ArrayIterator;
use Iterator;
use Tebru\Gson\Element\JsonArray;
use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\Element\JsonNull;
use Tebru\Gson\Element\JsonObject;
use Tebru\Gson\Element\JsonPrimitive;
use Tebru\Gson\JsonToken;

/**
 * Class JsonElementReader
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class JsonElementReader extends JsonReader
{
    /**
     * Constructor
     *
     * @param JsonElement $jsonElement
     */
    public function __construct(JsonElement $jsonElement)
    {
        $this->payload = $jsonElement;
        $this->push($jsonElement);
    }

    /**
     * Consumes the next token and asserts it's the beginning of a new array
     *
     * @return void
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not BEGIN_ARRAY
     */
    public function beginArray()
    {
        $this->expect(JsonToken::BEGIN_ARRAY);

        /** @var JsonArray $jsonArray */
        $jsonArray = $this->pop();
        $this->push($jsonArray->getIterator(), ArrayIterator::class);
        $this->pathIndices[$this->stackSize - 1] = 0;
    }

    /**
     * Consumes the next token and asserts it's the beginning of a new object
     *
     * @return void
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not BEGIN_OBJECT
     */
    public function beginObject()
    {
        $this->expect(JsonToken::BEGIN_OBJECT);

        /** @var JsonObject $jsonObject */
        $jsonObject = $this->pop();

        $this->push(new JsonObjectIterator($jsonObject), JsonObjectIterator::class);
    }

    /**
     * Consumes the value of the next token, asserts it's a boolean and returns it
     *
     * @return bool
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not BOOLEAN
     */
    public function nextBoolean()
    {
        $this->expect(JsonToken::BOOLEAN);

        /** @var JsonPrimitive $primitive */
        $primitive = $this->pop();

        $this->incrementPathIndex();

        return $primitive->asBoolean();
    }

    /**
     * Consumes the value of the next token, asserts it's a double and returns it
     *
     * @return double
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not NUMBER
     */
    public function nextDouble()
    {
        $this->expect(JsonToken::NUMBER);

        /** @var JsonPrimitive $primitive */
        $primitive = $this->pop();

        $this->incrementPathIndex();

        return $primitive->asFloat();
    }

    /**
     * Consumes the value of the next token, asserts it's an int and returns it
     *
     * @return int
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not NUMBER
     */
    public function nextInteger()
    {
        $this->expect(JsonToken::NUMBER);

        /** @var JsonPrimitive $primitive */
        $primitive = $this->pop();

        $this->incrementPathIndex();

        return $primitive->asInteger();
    }

    /**
     * Consumes the value of the next token, asserts it's a string and returns it
     *
     * @return string
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the next token is not NAME or STRING
     */
    public function nextString()
    {
        $peek = $this->peek();
        if ($peek === JsonToken::NAME) {
            return $this->nextName();
        }

        $this->expect(JsonToken::STRING);

        /** @var JsonPrimitive $primitive */
        $primitive = $this->pop();

        $this->incrementPathIndex();

        return $primitive->asString();
    }

    /**
     * Returns an enum representing the type of the next token without consuming it
     *
     * @return string
     */
    public function peek()
    {
        if (null !== $this->currentToken) {
            return $this->currentToken;
        }

        if (0 === $this->stackSize) {
            $this->currentToken = JsonToken::END_DOCUMENT;

            return $this->currentToken;
        }

        $token = null;
        $element = $this->stack[$this->stackSize - 1];

        switch ($this->stackTypes[$this->stackSize - 1]) {
            case JsonArray::class:
                $token = JsonToken::BEGIN_ARRAY;
                break;
            case JsonNull::class:
                $token = JsonToken::NULL;
                break;
            case JsonObject::class:
                $token = JsonToken::BEGIN_OBJECT;
                break;
            case JsonPrimitive::class:
                /** @var JsonPrimitive $element */
                if ($element->isString()) {
                    $token = JsonToken::STRING;
                } elseif ($element->isBoolean()) {
                    $token = JsonToken::BOOLEAN;
                } elseif ($element->isNumber()) {
                    $token = JsonToken::NUMBER;
                }

                break;
            case JsonObjectIterator::class:
                /** @var JsonObjectIterator $element */
                $token = $element->valid() ? JsonToken::NAME : JsonToken::END_OBJECT;

                break;
            case ArrayIterator::class:
                /** @var ArrayIterator $element */
                if ($element->valid()) {
                    $this->push($element->current());
                    $element->next();

                    $token = $this->peek();
                } else {
                    $token = JsonToken::END_ARRAY;
                }

                break;
        }

        $this->currentToken = $token;

        return $this->currentToken;
    }

    /**
     * Get the current read path in json xpath format
     *
     * @return string
     */
    public function getPath()
    {
        $result = '$';
        foreach ($this->stack as $index => $item) {
            if ($item instanceof ArrayIterator && isset($this->pathIndices[$index])) {
                $result .= '['.$this->pathIndices[$index].']';
            }

            if ($item instanceof JsonObjectIterator && isset($this->pathNames[$index])) {
                $result .= '.'.$this->pathNames[$index];
            }
        }

        return $result;
    }

    /**
     * Push an element onto the stack
     *
     * @param JsonElement|Iterator $element
     * @param string|null $type
     */
    protected function push($element, $type = null)
    {
        if (null === $type) {
            $type = get_class($element);
        }

        $this->stack[$this->stackSize] = $element;
        $this->stackTypes[$this->stackSize] = $type;
        $this->stackSize++;
        $this->currentToken = null;
    }
}
