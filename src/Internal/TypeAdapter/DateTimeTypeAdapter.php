<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter;

use DateTime;
use Tebru\Gson\Exception\JsonSyntaxException;
use Tebru\Gson\JsonWritable;
use Tebru\Gson\JsonReadable;
use Tebru\Gson\JsonToken;
use Tebru\Gson\TypeAdapter;
use Tebru\PhpType\TypeToken;

/**
 * Class DateTimeTypeAdapter
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class DateTimeTypeAdapter extends TypeAdapter
{
    /**
     * @var TypeToken
     */
    private $type;

    /**
     * @var string
     */
    private $format;

    /**
     * Constructor
     *
     * @param TypeToken $type
     * @param string $format
     */
    public function __construct(TypeToken $type, $format)
    {
        $this->type = $type;
        $this->format = $format;
    }

    /**
     * Read the next value, convert it to its type and return it
     *
     * @param JsonReadable $reader
     * @return DateTime|null
     * @throws \Tebru\Gson\Exception\JsonSyntaxException If the DateTime could not be created from format
     */
    public function read(JsonReadable $reader)
    {
        if ($reader->peek() === JsonToken::NULL) {
            return $reader->nextNull();
        }

        $formattedDateTime = $reader->nextString();

        /** @var DateTime $class */
        $class = $this->type->getRawType();

        $dateTime = $class::createFromFormat($this->format, $formattedDateTime);

        if ($dateTime !== false) {
            return $dateTime;
        }

        throw new JsonSyntaxException(sprintf(
            'Could not create "%s" class from "%s" using format "%s" at "%s"',
            $class,
            $formattedDateTime,
            $this->format,
            $reader->getPath()
        ));
    }

    /**
     * Write the value to the writer for the type
     *
     * @param JsonWritable $writer
     * @param DateTime $value
     * @return void
     */
    public function write(JsonWritable $writer, $value)
    {
        if (null === $value) {
            $writer->writeNull();

            return;
        }

        $dateTime = $value->format($this->format);
        $writer->writeString($dateTime);
    }
}
