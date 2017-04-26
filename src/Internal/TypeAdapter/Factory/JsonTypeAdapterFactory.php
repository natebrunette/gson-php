<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter\Factory;

use Tebru\Gson\Annotation\JsonAdapter;
use Tebru\Gson\Internal\Data\AnnotationCollectionFactory;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;
use Tebru\PhpType\TypeToken;

/**
 * Class JsonTypeAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class JsonTypeAdapterFactory implements TypeAdapterFactory
{
    /**
     * @var AnnotationCollectionFactory
     */
    private $annotationCollectionFactory;

    /**
     * Constructor
     *
     * @param AnnotationCollectionFactory $annotationCollectionFactory
     */
    public function __construct(AnnotationCollectionFactory $annotationCollectionFactory)
    {
        $this->annotationCollectionFactory = $annotationCollectionFactory;
    }

    /**
     * Will be called before ::create() is called.  The current type will be passed
     * in.  Return false if ::create() should not be called.
     *
     * @param TypeToken $type
     * @return bool
     */
    public function supports(TypeToken $type)
    {
        if (!$type->isObject()) {
            return false;
        }

        if (!class_exists($type->getRawType())) {
            return false;
        }

        $annotations = $this->annotationCollectionFactory->createClassAnnotations($type->getRawType());

        return null !== $annotations->getAnnotation(JsonAdapter::class, AnnotationSet::TYPE_CLASS);
    }

    /**
     * Accepts the current type and a [@see TypeAdapterProvider] in case another type adapter needs
     * to be fetched during creation.  Should return a new instance of the TypeAdapter.
     *
     * @param TypeToken $type
     * @param TypeAdapterProvider $typeAdapterProvider
     * @return TypeAdapter
     * @throws \InvalidArgumentException
     * @throws \Tebru\PhpType\Exception\MalformedTypeException If the type cannot be parsed
     */
    public function create(TypeToken $type, TypeAdapterProvider $typeAdapterProvider)
    {
        $annotations = $this->annotationCollectionFactory->createClassAnnotations($type->getRawType());

        /** @var JsonAdapter $annotation */
        $annotation = $annotations->getAnnotation(JsonAdapter::class, AnnotationSet::TYPE_CLASS);

        return $typeAdapterProvider->getAdapterFromAnnotation($type, $annotation);
    }
}
