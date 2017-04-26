<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter;

use Tebru\Gson\JsonWritable;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonToken;
use Tebru\Gson\TypeAdapter;

/**
 * Class StringTypeAdapter
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class StringTypeAdapter extends TypeAdapter
{
    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return string|null
     */
    public function read(JsonReadable $reader)
    {
        if ($reader->peek() === JsonToken::NULL) {
            return $reader->nextNull();
        }

        return $reader->nextString();
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param string $value
     * @return void
     */
    public function write(JsonWritable $writer, $value)
    {
        if (null === $value) {
            $writer->writeNull();

            return;
        }

        $writer->writeString((string)$value);
    }
}
