<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock\TypeAdapter;

use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\Element\JsonPrimitive;
use Tebru\Gson\PhpType;
use Tebru\Gson\JsonDeserializationContext;
use Tebru\Gson\JsonDeserializer;
use Tebru\Gson\JsonSerializationContext;
use Tebru\Gson\JsonSerializer;
use Tebru\Gson\Test\Mock\GsonObjectMock;

/**
 * Class GsonObjectMockSerializerMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
class GsonObjectMockSerializerMock implements JsonSerializer, JsonDeserializer
{
    /**
     * Called during deserialization process, passing in the JsonElement for the type.  Use
     * the JsonDeserializationContext if you want to delegate deserialization of sub types.
     *
     * @param JsonElement $jsonElement
     * @param PhpType $type
     * @param JsonDeserializationContext $context
     * @return mixed
     */
    public function deserialize(JsonElement $jsonElement, PhpType $type, JsonDeserializationContext $context): GsonObjectMock
    {
        return new GsonObjectMock($jsonElement->asString());
    }

    /**
     * Called during serialization process, passing in the object and type that should
     * be serialized.  Delegate serialization using the provided context.  Method should
     * return a JsonElement.
     *
     * @param GsonObjectMock $object
     * @param PhpType $type
     * @param JsonSerializationContext $context
     * @return JsonElement
     */
    public function serialize($object, PhpType $type, JsonSerializationContext $context): JsonElement
    {
        return JsonPrimitive::create($object->getFoo());
    }
}
