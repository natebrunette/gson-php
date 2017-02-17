<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock;

use DateTime;
use Tebru\Gson\Annotation as Gson;
use Tebru\Gson\Annotation\Accessor;
use Tebru\Gson\Annotation\Exclude;
use Tebru\Gson\Annotation\Expose;
use Tebru\Gson\Annotation\JsonAdapter;
use Tebru\Gson\Annotation\SerializedName;
use Tebru\Gson\Annotation\Since;
use Tebru\Gson\Annotation\Type;
use Tebru\Gson\Annotation\Until;
use Tebru\Gson\Annotation\VirtualProperty;

/**
 * Class GsonMock
 *
 * @author Nate Brunette <n@tebru.net>
 * @Expose()
 */
class GsonMock
{
    /**
     * @Type("int")
     */
    private $integer;
    private $float;
    private $string;
    private $boolean;
    private $array;
    private $date;

    public $public;
    protected $protected;

    /**
     * @Since(2)
     */
    private $since;

    /**
     * @Until(2)
     */
    private $until;

    /**
     * @Accessor(get="getMyAccessor", set="setMyAccessor")
     */
    private $accessor;

    /**
     * @SerializedName("serialized_name")
     */
    private $serializedname;

    /**
     * @Type("array<int>")
     */
    private $type;

    /**
     * @JsonAdapter("Tebru\Gson\Test\Mock\TypeAdapter\GsonObjectMockSerializerMock")
     */
    private $jsonAdapter;

    /**
     * @Expose()
     */
    private $expose;

    /**
     * @Exclude()
     */
    private $exclude;

    private $excludeFromStrategy;

    private $gsonObjectMock;

    /**
     * @Type("Tebru\Gson\Test\Mock\GsonMock")
     * @Exclude()
     */
    private $circular;

    /**
     * @Type("Tebru\Gson\Test\Mock\ExcludedClassMock")
     */
    private $excludedClass;

    /**
     * @Type("CustomType")
     */
    private $pseudoClass;

    public function getInteger()
    {
        if (null === $this->integer) {
            return null;
        }

        return (int) $this->integer;
    }

    public function setInteger($integer)
    {
        $this->integer = $integer;

        return $this;
    }

    public function getFloat()
    {
        return $this->float;
    }

    public function setFloat($float)
    {
        $this->float = $float;

        return $this;
    }

    public function getString()
    {
        return $this->string;
    }

    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }

    public function getBoolean()
    {
        return $this->boolean;
    }

    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;

        return $this;
    }

    public function getArray()
    {
        return $this->array;
    }

    public function setArray(array $array)
    {
        $this->array = $array;

        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate(DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    public function getSince()
    {
        return $this->since;
    }

    public function setSince($since)
    {
        $this->since = $since;

        return $this;
    }

    public function getUntil()
    {
        return $this->until;
    }

    public function setUntil($until)
    {
        $this->until = $until;

        return $this;
    }

    public function getMyAccessor()
    {
        return $this->accessor;
    }

    public function setMyAccessor($accessor)
    {
        $this->accessor = $accessor;

        return $this;
    }

    public function getSerializedname()
    {
        return $this->serializedname;
    }

    public function setSerializedname($serializedname)
    {
        $this->serializedname = $serializedname;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType(array $type)
    {
        $this->type = $type;

        return $this;
    }

    public function getJsonAdapter()
    {
        return $this->jsonAdapter;
    }

    public function setJsonAdapter(GsonObjectMock $jsonAdapter)
    {
        $this->jsonAdapter = $jsonAdapter;

        return $this;
    }

    public function getExpose()
    {
        return $this->expose;
    }

    public function setExpose($expose)
    {
        $this->expose = $expose;

        return $this;
    }

    public function getExclude()
    {
        return $this->exclude;
    }

    public function setExclude($exclude)
    {
        $this->exclude = $exclude;

        return $this;
    }

    public function getExcludeFromStrategy()
    {
        return $this->excludeFromStrategy;
    }

    public function setExcludeFromStrategy($excludeFromStrategy)
    {
        $this->excludeFromStrategy = $excludeFromStrategy;

        return $this;
    }

    public function getGsonObjectMock()
    {
        return $this->gsonObjectMock;
    }

    public function setGsonObjectMock(GsonObjectMock $gsonObjectMock)
    {
        $this->gsonObjectMock = $gsonObjectMock;

        return $this;
    }

    public function getProtectedHidden()
    {
        return $this->protected;
    }

    public function setProtectedHidden($protected)
    {
        $this->protected = $protected;
    }

    /**
     * @VirtualProperty()
     * @SerializedName("virtual")
     */
    public function myVirtualProperty()
    {
        return 2;
    }
}
