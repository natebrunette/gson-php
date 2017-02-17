<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson;

use Tebru\Gson\Internal\Data\AnnotationSet;

/**
 * Interface PropertyMetadata
 *
 * Represents a property and its annotations
 *
 * @author Nate Brunette <n@tebru.net>
 */
interface PropertyMetadata
{
    /**
     * Get the property name
     *
     * @return string
     */
    public function getName();

    /**
     * Get the property serialized name
     *
     * @return string
     */
    public function getSerializedName();

    /**
     * Get the full php type object
     *
     * @return PhpType
     */
    public function getType();

    /**
     * Get the property type as a string
     *
     * @return string
     */
    public function getTypeName();

    /**
     * Get the property modifiers as a bitmap of [@see \ReflectionProperty] constants
     *
     * @return int
     */
    public function getModifiers();

    /**
     * Get full declaring class metadata
     *
     * @return ClassMetadata
     */
    public function getDeclaringClassMetadata();

    /**
     * Get the declaring class name
     *
     * @return string
     */
    public function getDeclaringClassName();

    /**
     * Get property annotations
     *
     * @return AnnotationSet
     */
    public function getAnnotations();

    /**
     * Get a single annotation, returns null if the annotation doesn't exist
     *
     * @param string $annotationName
     * @return null|object
     */
    public function getAnnotation($annotationName);

    /**
     * Returns true if the property is virtual
     *
     * @return bool
     */
    public function isVirtual();
}
