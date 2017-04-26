<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\Data;

use Tebru\Gson\Internal\GetterStrategy;
use Tebru\Gson\Internal\SetterStrategy;
use Tebru\PhpType\TypeToken;

/**
 * Class Property
 *
 * Represents static information about an object property.  Instances of this class may be
 * cached for later use.
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class Property
{
    /**
     * The actual name of the property
     *
     * @var string
     */
    private $realName;

    /**
     * The serialized version of the property name
     *
     * @var string
     */
    private $serializedName;

    /**
     * The property type
     *
     * @var TypeToken
     */
    private $type;

    /**
     * The method for getting values from this property
     *
     * @var GetterStrategy
     */
    private $getterStrategy;

    /**
     * The method for setting values to this property
     *
     * @var SetterStrategy
     */
    private $setterStrategy;

    /**
     * A set of annotations
     *
     * @var AnnotationSet
     */
    private $annotations;

    /**
     * An integer that represents what modifiers are associated with the property
     *
     * These constants are defined in [@see \ReflectionProperty]
     *
     * @var int
     */
    private $modifiers;

    /**
     * True if the property should be skipped during serialization
     *
     * @var bool
     */
    private $skipSerialize = false;

    /**
     * True if the property should be skipped during deserialization
     *
     * @var bool
     */
    private $skipDeserialize = false;

    /**
     * If the property is a virtual property
     * @var bool
     */
    private $virtual;

    /**
     * Constructor
     *
     * @param string $realName
     * @param string $serializedName
     * @param TypeToken $type
     * @param GetterStrategy $getterStrategy
     * @param SetterStrategy $setterStrategy
     * @param AnnotationSet $annotations
     * @param int $modifiers
     * @param bool $virtual
     */
    public function __construct(
        $realName,
        $serializedName,
        TypeToken $type,
        GetterStrategy $getterStrategy,
        SetterStrategy $setterStrategy,
        AnnotationSet $annotations,
        $modifiers,
        $virtual
    )
    {
        $this->realName = $realName;
        $this->serializedName = $serializedName;
        $this->type = $type;
        $this->getterStrategy = $getterStrategy;
        $this->setterStrategy = $setterStrategy;
        $this->annotations = $annotations;
        $this->modifiers = $modifiers;
        $this->virtual = $virtual;
    }

    /**
     * Get the real name of the property
     *
     * @return string
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Get the serialized name of the property
     *
     * @return string
     */
    public function getSerializedName()
    {
        return $this->serializedName;
    }

    /**
     * Get the property type
     *
     * @return TypeToken
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return the collection of annotations
     *
     * @return AnnotationSet
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * The property modifiers
     *
     * @return int
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * Returns true if the property is virtual
     *
     * @return bool
     */
    public function isVirtual()
    {
        return $this->virtual;
    }

    /**
     * Returns should if we should skip during serialization
     *
     * @return bool
     */
    public function skipSerialize()
    {
        return $this->skipSerialize;
    }

    /**
     * Set whether we should skip during serialization
     *
     * @param bool $skipSerialize
     */
    public function setSkipSerialize($skipSerialize)
    {
        $this->skipSerialize = $skipSerialize;
    }

    /**
     * Returns should if we should skip during deserialization
     *
     * @return bool
     */
    public function skipDeserialize()
    {
        return $this->skipDeserialize;
    }

    /**
     * Set whether we should skip during deserialization
     *
     * @param bool $skipDeserialize
     */
    public function setSkipDeserialize($skipDeserialize)
    {
        $this->skipDeserialize = $skipDeserialize;
    }

    /**
     * Given an object, get the value at this property
     *
     * @param object $object
     * @return mixed
     */
    public function get($object)
    {
        return $this->getterStrategy->get($object);
    }

    /**
     * Given an object and value, set the value to the object at this property
     *
     * @param object $object
     * @param mixed $value
     */
    public function set($object, $value)
    {
        $this->setterStrategy->set($object, $value);
    }
}
