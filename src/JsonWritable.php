<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson;

/**
 * Interface JsonWritable
 *
 * @author Nate Brunette <n@tebru.net>
 */
interface JsonWritable
{
    /**
     * Begin writing array
     *
     * @return JsonWritable
     */
    public function beginArray();

    /**
     * End writing array
     *
     * @return JsonWritable
     */
    public function endArray();

    /**
     * Begin writing object
     *
     * @return JsonWritable
     */
    public function beginObject();

    /**
     * End writing object
     *
     * @return JsonWritable
     */
    public function endObject();

    /**
     * Writes a property name
     *
     * @param string $name
     * @return JsonWritable
     */
    public function name($name);

    /**
     * Write an integer value
     *
     * @param int $value
     * @return JsonWritable
     */
    public function writeInteger($value);

    /**
     * Write a float value
     *
     * @param float $value
     * @return JsonWritable
     */
    public function writeFloat($value);

    /**
     * Write a string value
     *
     * @param string $value
     * @return JsonWritable
     */
    public function writeString($value);

    /**
     * Write a boolean value
     *
     * @param boolean $value
     * @return JsonWritable
     */
    public function writeBoolean($value);

    /**
     * Write a null value if we are serializing nulls, otherwise
     * skip the value.  If this is a property value, that property
     * should be skipped as well.
     *
     * @return JsonWritable
     */
    public function writeNull();

    /**
     * Sets whether nulls are serialized
     *
     * @param bool $serializeNull
     */
    public function setSerializeNull($serializeNull);
}
