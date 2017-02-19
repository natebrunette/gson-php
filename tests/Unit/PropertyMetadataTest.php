<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson\Test\Unit;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Tebru\Gson\ClassMetadata;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\PhpType;
use Tebru\Gson\PropertyMetadata;
use Tebru\Gson\Test\Mock\Annotation\FooAnnotation;
use Tebru\Gson\Test\Mock\Foo;

/**
 * Class PropertyMetadataTest
 *
 * @author Nate Brunette <n@tebru.net>
 */
class PropertyMetadataTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var ClassMetadata
     */
    private $classMetadata;

    /**
     * @var AnnotationSet
     */
    private $annotations;

    /**
     * @var PropertyMetadata
     */
    private $propertyMetadata;

    /**
     * @var PropertyMetadata
     */
    private $virtualPropertyMetadata;

    public function setUp()
    {
        $this->classMetadata = new ClassMetadata(Foo::class, new AnnotationSet());
        $this->annotations = new AnnotationSet();
        $this->propertyMetadata = new PropertyMetadata(
            'foo',
            'foo',
            new PhpType('string'),
            ReflectionProperty::IS_PUBLIC,
            $this->classMetadata,
            $this->annotations,
            false
        );

        $this->virtualPropertyMetadata = new PropertyMetadata(
            'foo',
            'foo',
            new PhpType('string'),
            ReflectionProperty::IS_PUBLIC,
            $this->classMetadata,
            $this->annotations,
            true
        );
    }

    public function testGetters()
    {
        self::assertSame('foo', $this->propertyMetadata->getName());
        self::assertSame('foo', $this->propertyMetadata->getSerializedName());
        self::assertSame('string', (string) $this->propertyMetadata->getType());
        self::assertSame('string', $this->propertyMetadata->getTypeName());
        self::assertSame(ReflectionProperty::IS_PUBLIC, $this->propertyMetadata->getModifiers());
        self::assertSame($this->classMetadata, $this->propertyMetadata->getDeclaringClassMetadata());
        self::assertSame(Foo::class, $this->propertyMetadata->getDeclaringClassName());
        self::assertSame($this->annotations, $this->propertyMetadata->getAnnotations());
        self::assertFalse($this->propertyMetadata->isVirtual());
    }

    public function testGetAnnotation()
    {
        $annotation = new FooAnnotation([]);
        $this->annotations->addAnnotation($annotation, AnnotationSet::TYPE_PROPERTY);

        self::assertSame($annotation, $this->propertyMetadata->getAnnotation(FooAnnotation::class));
    }

    public function testGetAnnotationNull()
    {
        self::assertNull($this->propertyMetadata->getAnnotation(FooAnnotation::class));
    }

    public function testGetAnnotationVirtual()
    {
        $annotation = new FooAnnotation([]);
        $this->annotations->addAnnotation($annotation, AnnotationSet::TYPE_METHOD);


        self::assertSame($annotation, $this->virtualPropertyMetadata->getAnnotation(FooAnnotation::class));
    }

    public function testGetAnnotationVirtualNull()
    {
        self::assertNull($this->virtualPropertyMetadata->getAnnotation(FooAnnotation::class));
    }
}
