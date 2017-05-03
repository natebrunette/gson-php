<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use ReflectionProperty;
use Tebru\Gson\Annotation\Exclude;
use Tebru\Gson\Annotation\Expose;
use Tebru\Gson\Annotation\Since;
use Tebru\Gson\Annotation\Until;
use Tebru\Gson\ClassMetadata;
use Tebru\Gson\ExclusionData;
use Tebru\Gson\ExclusionStrategy;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\PropertyMetadata;

/**
 * Class Excluder
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class Excluder
{
    /**
     * Version, if set, will be used with [@see Since] and [@see Until] annotations
     *
     * @var string
     */
    private $version;

    /**
     * Which modifiers are excluded
     *
     * By default only static properties are excluded
     *
     * @var int
     */
    private $excludedModifiers = ReflectionProperty::IS_STATIC;

    /**
     * If this is true, properties will need to explicitly have an [@see Expose] annotation
     * to be serialized or deserialized
     *
     * @var bool
     */
    private $requireExpose = false;

    /**
     * Exclusions strategies during serialization
     *
     * @var ExclusionStrategy[]
     */
    private $serializationStrategies = [];

    /**
     * Exclusion strategies during deserialization
     *
     * @var ExclusionStrategy[]
     */
    private $deserializationStrategies = [];


    /**
     * Set the version to test against
     *
     * @param string $version
     * @return Excluder
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Set an integer representing the property modifiers that should be excluded
     *
     * @param int $modifiers
     * @return Excluder
     */
    public function setExcludedModifiers($modifiers)
    {
        $this->excludedModifiers = $modifiers;

        return $this;
    }

    /**
     * Require the [@see Expose] annotation to serialize properties
     *
     * @param bool $requireExpose
     * @return Excluder
     */
    public function setRequireExpose($requireExpose)
    {
        $this->requireExpose = $requireExpose;

        return $this;
    }

    /**
     * Add an exclusion strategy and specify if it should be used during serialization or deserialization
     *
     * @param ExclusionStrategy $strategy
     * @param bool $serialization
     * @param bool $deserialization
     */
    public function addExclusionStrategy(ExclusionStrategy $strategy, $serialization, $deserialization)
    {
        if ($serialization) {
            $this->serializationStrategies[] = $strategy;
        }

        if ($deserialization) {
            $this->deserializationStrategies[] = $strategy;
        }
    }

    /**
     * Returns true if we should exclude the class for a given serialization direction
     *
     * @param ClassMetadata $classMetadata
     * @param bool $serialize
     * @return bool
     */
    public function excludeClass(ClassMetadata $classMetadata, $serialize)
    {
        return $this->excludeByAnnotation($classMetadata->getAnnotations(), $serialize, AnnotationSet::TYPE_CLASS);
    }

    /**
     * @param ClassMetadata $classMetadata
     * @param bool $serialize
     * @return bool
     */
    public function excludeClassByStrategy(ClassMetadata $classMetadata, $serialize)
    {
        $strategies = $serialize ? $this->serializationStrategies : $this->deserializationStrategies;
        foreach ($strategies as $exclusionStrategy) {
            if ($exclusionStrategy->shouldSkipClass($classMetadata)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if we should exclude the class for a given serialization direction
     *
     * @param PropertyMetadata $propertyMetadata
     * @param bool $serialize
     * @return bool
     */
    public function excludeProperty(PropertyMetadata $propertyMetadata, $serialize)
    {
        // exclude the property if the property modifiers are found in the excluded modifiers
        if (0 !== ($this->excludedModifiers & $propertyMetadata->getModifiers())) {
            return true;
        }

        $filter = $propertyMetadata->isVirtual() ? AnnotationSet::TYPE_METHOD : AnnotationSet::TYPE_PROPERTY;

        return $this->excludeByAnnotation($propertyMetadata->getAnnotations(), $serialize, $filter);
    }

    /**
     * Returns true if we should exclude the class for a given serialization direction
     *
     * Uses user-defined strategies
     *
     * @param PropertyMetadata $property
     * @param ExclusionData $exclusionData
     * @return bool
     */
    public function excludePropertyByStrategy(PropertyMetadata $property, ExclusionData $exclusionData)
    {
        $strategies = $exclusionData->isSerialize() ? $this->serializationStrategies : $this->deserializationStrategies;
        foreach ($strategies as $exclusionStrategy) {
            if ($exclusionStrategy->shouldSkipProperty($property, $exclusionData)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Checks various annotations to see if the property should be excluded
     *
     * - [@see Since] / [@see Until]
     * - [@see Exclude]
     * - [@see Expose] (if requireExpose is set)
     *
     * @param AnnotationSet $annotations
     * @param bool $serialize
     * @param int $filter
     * @return bool
     */
    private function excludeByAnnotation(AnnotationSet $annotations, $serialize, $filter)
    {
        if (!$this->validVersion($annotations, $filter)) {
            return true;
        }

        /** @var Exclude $exclude */
        $exclude = $annotations->getAnnotation(Exclude::class, $filter);
        if (null !== $exclude && $exclude->shouldExclude($serialize)) {
            return true;
        }

        // if we need an expose annotation
        if ($this->requireExpose) {
            /** @var Expose $expose */
            $expose = $annotations->getAnnotation(Expose::class, $filter);
            if (null === $expose || !$expose->shouldExpose($serialize)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns true if the set version is valid for [@see Since] and [@see Until] annotations
     *
     * @param AnnotationSet $annotations
     * @param int $filter
     * @return bool
     */
    private function validVersion(AnnotationSet $annotations, $filter)
    {
        return !$this->shouldSkipSince($annotations, $filter) && !$this->shouldSkipUntil($annotations, $filter);
    }

    /**
     * Returns true if we should skip based on the [@see Since] annotation
     *
     * @param AnnotationSet $annotations
     * @param int $filter
     * @return bool
     */
    private function shouldSkipSince(AnnotationSet $annotations, $filter)
    {
        /** @var Since $sinceAnnotation */
        $sinceAnnotation = $annotations->getAnnotation(Since::class, $filter);

        return
            null !== $sinceAnnotation
            && null !== $this->version
            && version_compare($this->version, $sinceAnnotation->getVersion(), '<');
    }

    /**
     * Returns true if we should skip based on the [@see Until] annotation
     *
     * @param AnnotationSet $annotations
     * @param int $filter
     * @return bool
     */
    private function shouldSkipUntil(AnnotationSet $annotations, $filter)
    {
        /** @var Until $sinceAnnotation */
        $untilAnnotation = $annotations->getAnnotation(Until::class, $filter);

        return
            null !== $untilAnnotation
            && null !== $this->version
            && version_compare($this->version, $untilAnnotation->getVersion(), '>=');
    }
}
