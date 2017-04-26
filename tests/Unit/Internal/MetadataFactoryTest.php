<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson\Test\Unit\Internal;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\VoidCache;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Tebru\Gson\ClassMetadata;
use Tebru\Gson\Internal\AccessorStrategy\GetByPublicProperty;
use Tebru\Gson\Internal\AccessorStrategy\SetByPublicProperty;
use Tebru\Gson\Internal\Data\AnnotationCollectionFactory;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\Internal\Data\Property;
use Tebru\Gson\Internal\MetadataFactory;
use Tebru\Gson\PropertyMetadata;
use Tebru\Gson\Test\Mock\Foo;
use Tebru\PhpType\TypeToken;

/**
 * Class MetadataFactoryTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Internal\MetadataFactory
 */
class MetadataFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Property
     */
    private $defaultProperty;

    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    public function setUp()
    {
        $this->metadataFactory = new MetadataFactory(new AnnotationCollectionFactory(new AnnotationReader(), new VoidCache()));
        $this->defaultProperty = $defaultProperty = new Property(
            'foo',
            'foo',
            new TypeToken('string'),
            new GetByPublicProperty('foo'),
            new SetByPublicProperty('foo'),
            new AnnotationSet(),
            ReflectionProperty::IS_PUBLIC,
            false
        );
    }

    public function testCreateClassMetadata()
    {
        $metadata = $this->metadataFactory->createClassMetadata(Foo::class);

        self::assertInstanceOf(ClassMetadata::class, $metadata);
        self::assertSame(Foo::class, $metadata->getName());
        self::assertEquals(new AnnotationSet(), $metadata->getAnnotations());
    }

    public function testCreatePropertyMetadata()
    {
        $classMetadata = $this->metadataFactory->createClassMetadata(Foo::class);
        $metadata = $this->metadataFactory->createPropertyMetadata($this->defaultProperty, $classMetadata);

        self::assertInstanceOf(PropertyMetadata::class, $metadata);
        self::assertSame('foo', $metadata->getName());
        self::assertSame($classMetadata, $metadata->getDeclaringClassMetadata());
    }
}
