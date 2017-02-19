<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal\TypeAdapter\Factory;

use Tebru\Gson\Internal\ConstructorConstructor;
use Tebru\Gson\Internal\Data\MetadataPropertyCollection;
use Tebru\Gson\Internal\Data\Property;
use Tebru\Gson\Internal\MetadataFactory;
use Tebru\Gson\PhpType;
use Tebru\Gson\Internal\Data\PropertyCollectionFactory;
use Tebru\Gson\Internal\TypeAdapter\ReflectionTypeAdapter;
use Tebru\Gson\Internal\TypeAdapterProvider;
use Tebru\Gson\TypeAdapter;
use Tebru\Gson\TypeAdapterFactory;

/**
 * Class ReflectionTypeAdapterFactory
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class ReflectionTypeAdapterFactory implements TypeAdapterFactory
{
    /**
     * @var ConstructorConstructor
     */
    private $constructorConstructor;

    /**
     * @var PropertyCollectionFactory
     */
    private $propertyCollectionFactory;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * Constructor
     *
     * @param ConstructorConstructor $constructorConstructor
     * @param PropertyCollectionFactory $propertyCollectionFactory
     * @param MetadataFactory $metadataFactory
     */
    public function __construct(
        ConstructorConstructor $constructorConstructor,
        PropertyCollectionFactory $propertyCollectionFactory,
        MetadataFactory $metadataFactory
    ) {
        $this->constructorConstructor = $constructorConstructor;
        $this->propertyCollectionFactory = $propertyCollectionFactory;
        $this->metadataFactory = $metadataFactory;
    }

    /**
     * Will be called before ::create() is called.  The current type will be passed
     * in.  Return false if ::create() should not be called.
     *
     * @param PhpType $type
     * @return bool
     */
    public function supports(PhpType $type): bool
    {
        return $type->isObject();
    }

    /**
     * Accepts the current type.  Should return a new instance of the TypeAdapter.
     *
     * @param PhpType $type
     * @param TypeAdapterProvider $typeAdapterProvider
     * @return TypeAdapter
     * @throws \RuntimeException If the value is not valid
     * @throws \Tebru\Gson\Exception\MalformedTypeException If the type cannot be parsed
     * @throws \InvalidArgumentException if the type cannot be handled by a type adapter
     */
    public function create(PhpType $type, TypeAdapterProvider $typeAdapterProvider): TypeAdapter
    {
        $properties = $this->propertyCollectionFactory->create($type, $typeAdapterProvider);
        $objectConstructor = $this->constructorConstructor->get($type);

        $classMetadata = $this->metadataFactory->createClassMetadata($type->getClass());
        $metadataPropertyCollection = new MetadataPropertyCollection();

        /** @var Property $property */
        foreach ($properties as $property) {
            $metadataPropertyCollection->add($this->metadataFactory->createPropertyMetadata($property, $classMetadata));
        }

        return new ReflectionTypeAdapter($objectConstructor, $properties, $metadataPropertyCollection, $classMetadata);
    }
}
