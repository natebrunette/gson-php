<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter;

use LogicException;
use Tebru\Collection\ArrayList;
use Tebru\Gson\Internal\JsonWritable;
use Tebru\Gson\Internal\PhpType;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\Internal\TypeToken;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonToken;
use Tebru\Gson\TypeAdapter;

/**
 * Class ArrayListTypeAdapter
 *
 * Maps json to an [@see ArrayList]
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class ArrayListTypeAdapter extends TypeAdapter
{
    /**
     * @var PhpType
     */
    private $phpType;

    /**
     * @var TypeAdapterProvider
     */
    private $typeAdapterProvider;

    /**
     * Constructor
     *
     * @param PhpType $phpType
     * @param TypeAdapterProvider $typeAdapterProvider
     */
    public function __construct(PhpType $phpType, TypeAdapterProvider $typeAdapterProvider)
    {
        $this->phpType = $phpType;
        $this->typeAdapterProvider = $typeAdapterProvider;
    }

    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return ArrayList|null
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     * @throws \RuntimeException If the value is not valid
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \OutOfBoundsException if the index doesn't exist
     * @throws \LogicException If ArrayList contains more than one generic type
     */
    public function read(JsonReadable $reader): ?ArrayList
    {
        if ($reader->peek() === JsonToken::NULL) {
            return $reader->nextNull();
        }

        $arrayList = new ArrayList();

        $reader->beginArray();
        while ($reader->hasNext()) {
            $generics = $this->phpType->getGenerics();
            if (0 !== count($generics)) {
                if (1 !== count($generics)) {
                    throw new LogicException('ArrayList expected to have exactly one generic type');
                }

                $adapter = $this->typeAdapterProvider->getAdapter($generics[0]);
                $arrayList->add($adapter->read($reader));

                continue;
            }

            switch ($reader->peek()) {
                case JsonToken::BEGIN_ARRAY:
                    $type = new PhpType('List');
                    break;
                case JsonToken::BEGIN_OBJECT:
                    $type = new PhpType('Map');
                    break;
                default:
                    $type = new PhpType(TypeToken::WILDCARD);
            }

            $adapter = $this->typeAdapterProvider->getAdapter($type);
            $arrayList->add($adapter->read($reader));
        }
        $reader->endArray();

        return $arrayList;
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param mixed $value
     * @return void
     */
    public function write(JsonWritable $writer, $value): void
    {
    }
}