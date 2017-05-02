<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson;

use Tebru\Gson\Element\JsonElement;
use Tebru\Gson\Internal\ObjectConstructor\CreateFromInstance;
use Tebru\Gson\Internal\ObjectConstructorAware;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\PhpType\TypeToken;

/**
 * Class Gson
 *
 * @author Nate Brunette <n@tebru.net>
 */
class Gson
{
    /**
     * A service to fetch the correct [@see TypeAdapter] for a given type
     *
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * True if we should serialize nulls
     *
     * @var bool
     */
    private $serializeNull;

    /**
     * Constructor
     *
     * @param TypeAdapterProvider $typeAdapterProvider
     * @param bool $serializeNull
     */
    public function __construct(TypeAdapterProvider $typeAdapterProvider, $serializeNull)
    {
        $this->typeAdapterProvider = $typeAdapterProvider;
        $this->serializeNull = $serializeNull;
    }

    /**
     * Create a new builder object
     *
     * @return GsonBuilder
     */
    public static function builder()
    {
        return new GsonBuilder();
    }

    /**
     * Converts an object to a json string
     *
     * @param mixed $object
     * @return string
     * @throws \InvalidArgumentException
     */
    public function toJson($object)
    {
        $type = TypeToken::createFromVariable($object);
        $typeAdapter = $this->typeAdapterProvider->getAdapter($type);

        return $typeAdapter->writeToJson($object, $this->serializeNull);
    }

    /**
     * Converts a json string to a valid json type
     *
     * @param string $json
     * @param object|string $type
     * @return mixed
     * @throws \InvalidArgumentException
     * @throws \Tebru\PhpType\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \Tebru\Gson\Exception\JsonParseException If the json cannot be decoded
     */
    public function fromJson($json, $type)
    {
        $isObject = is_object($type);
        $typeToken = $isObject ? new TypeToken(get_class($type)) : new TypeToken($type);
        $typeAdapter = $this->typeAdapterProvider->getAdapter($typeToken);

        if ($isObject && $typeAdapter instanceof ObjectConstructorAware) {
            $typeAdapter->setObjectConstructor(new CreateFromInstance($type));
        }

        return $typeAdapter->readFromJson($json);
    }

    /**
     * Converts an object to a [@see JsonElement]
     *
     * This is a convenience method that first converts an object to json utilizing all of the
     * type adapters, then converts that json to a JsonElement.  From here you can modify the
     * JsonElement and call json_encode() on it to get json.
     *
     * @param mixed $object
     * @return JsonElement
     * @throws \InvalidArgumentException
     * @throws \Tebru\PhpType\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \Tebru\Gson\Exception\JsonParseException If the json cannot be decoded
     */
    public function toJsonElement($object)
    {
        return $this->fromJson($this->toJson($object), JsonElement::class);
    }
}
