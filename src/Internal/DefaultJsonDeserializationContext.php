<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\JsonDeserializationContext;
use Tebru\Gson\PhpType;

/**
 * Class DefaultJsonDeserializationContext
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class DefaultJsonDeserializationContext implements JsonDeserializationContext
{
    /**
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * Constructor
     *
     * @param TypeAdapterProvider $typeAdapterProvider
     */
    public function __construct(TypeAdapterProvider $typeAdapterProvider)
    {
        $this->typeAdapterProvider = $typeAdapterProvider;
    }

    /**
     * Delegate deserialization of a JsonElement.  Should not be called on the original
     * element as doing so will result in an infinite loop.  Should return a deserialized
     * object.
     *
     * @param JsonElement $jsonElement
     * @param string $type
     * @return mixed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     */
    public function deserialize(JsonElement $jsonElement, string $type)
    {
        $typeAdapter = $this->typeAdapterProvider->getAdapter(new PhpType($type));

        return $typeAdapter->readFromJsonElement($jsonElement);
    }
}
