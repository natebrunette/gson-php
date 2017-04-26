<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Unit\Internal;

use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Tebru\Gson\Annotation\Exclude;
use Tebru\Gson\Annotation\Expose;
use Tebru\Gson\Annotation\Since;
use Tebru\Gson\Annotation\Until;
use Tebru\Gson\Internal\DefaultClassMetadata;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\Internal\Excluder;
use Tebru\Gson\Internal\MetadataFactory;
use Tebru\Gson\Internal\DefaultPropertyMetadata;
use Tebru\Gson\Test\Mock\ExcluderVersionMock;
use Tebru\Gson\Test\Mock\ExclusionStrategies\BarPropertyExclusionStrategy;
use Tebru\Gson\Test\Mock\ExclusionStrategies\ExcludeClassMockExclusionStrategy;
use Tebru\Gson\Test\Mock\ExcluderExcludeSerializeMock;
use Tebru\Gson\Test\Mock\ExcluderExposeMock;
use Tebru\Gson\Test\Mock\ExclusionStrategies\FooExclusionStrategy;
use Tebru\Gson\Test\Mock\ExclusionStrategies\FooPropertyExclusionStrategy;
use Tebru\Gson\Test\Mock\Foo;
use Tebru\Gson\Test\MockProvider;
use Tebru\PhpType\TypeToken;

/**
 * Class ExcluderTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Internal\Excluder
 */
class ExcluderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Excluder
     */
    private $excluder;
    
    /**
     * @var MetadataFactory
     */
    private $metadataFactory;

    /**
     * Set up dependencies
     */
    public function setUp()
    {
        $this->excluder = MockProvider::excluder();
        $this->metadataFactory = MockProvider::metadataFactory();
    }

    public function testExcludeClassWithoutVersion()
    {
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertFalse($this->excluder->excludeClass($classMetadata, true));
        self::assertFalse($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithVersionLow()
    {
        $this->excluder->setVersion('0.1.0');
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);
        
        self::assertTrue($this->excluder->excludeClass($classMetadata, true));
        self::assertTrue($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithVersionEqualSince()
    {
        $this->excluder->setVersion('1');
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);
        
        self::assertFalse($this->excluder->excludeClass($classMetadata, true));
        self::assertFalse($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithVersionBetween()
    {
        $this->excluder->setVersion('1.5');
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertFalse($this->excluder->excludeClass($classMetadata, true));
        self::assertFalse($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithVersionEqualUntil()
    {
        $this->excluder->setVersion('2');
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertTrue($this->excluder->excludeClass($classMetadata, true));
        self::assertTrue($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithVersionHigh()
    {
        $this->excluder->setVersion('3');
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertTrue($this->excluder->excludeClass($classMetadata, true));
        self::assertTrue($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithExcludeAnnotation()
    {
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderExcludeSerializeMock::class);

        self::assertTrue($this->excluder->excludeClass($classMetadata, true));
        self::assertFalse($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithExposeAnnotation()
    {
        $this->excluder->setRequireExpose(true);
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderExposeMock::class);

        self::assertFalse($this->excluder->excludeClass($classMetadata, true));
        self::assertTrue($this->excluder->excludeClass($classMetadata, false));
    }

    public function testExcludeClassWithStrategySerialization()
    {
        $this->excluder->addExclusionStrategy(new FooExclusionStrategy(), true, false);
        $this->excluder->addExclusionStrategy(new ExcludeClassMockExclusionStrategy(), true, false);
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertTrue($this->excluder->excludeClassByStrategy($classMetadata, true));
        self::assertFalse($this->excluder->excludeClassByStrategy($classMetadata, false));
    }

    public function testExcludeClassWithStrategyDeserialization()
    {
        $this->excluder->addExclusionStrategy(new FooExclusionStrategy(), false, true);
        $this->excluder->addExclusionStrategy(new ExcludeClassMockExclusionStrategy(), false, true);
        $classMetadata = $this->metadataFactory->createClassMetadata(ExcluderVersionMock::class);

        self::assertFalse($this->excluder->excludeClassByStrategy($classMetadata, true));
        self::assertTrue($this->excluder->excludeClassByStrategy($classMetadata, false));
    }

    public function testExcludePropertyDefaultModifiers()
    {
        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PUBLIC | ReflectionProperty::IS_STATIC,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludePrivateProperties()
    {
        $this->excluder->setExcludedModifiers(ReflectionProperty::IS_STATIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeProtectedProperties()
    {
        $this->excluder->setExcludedModifiers(ReflectionProperty::IS_STATIC | ReflectionProperty::IS_PROTECTED | ReflectionProperty::IS_PRIVATE);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PROTECTED,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithoutSinceUntilAnnotations()
    {
        $this->excluder->setVersion(1);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithoutVersion()
    {
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Until(['value' => '2']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeBeforeSince()
    {
        $this->excluder->setVersion('1.0.0');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Since(['value' => '1.0.1']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeEqualToSince()
    {
        $this->excluder->setVersion('1.0.1');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Since(['value' => '1.0.1']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeAfterSince()
    {
        $this->excluder->setVersion('1.0.2');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Since(['value' => '1.0.1']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeBeforeUntil()
    {
        $this->excluder->setVersion('1');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Until(['value' => '2.0.0']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeEqualToUntil()
    {
        $this->excluder->setVersion('2.0.0');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Until(['value' => '2.0.0']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeGreaterThanUntil()
    {
        $this->excluder->setVersion('2.0.1');

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Until(['value' => '2.0.0']), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeWithExcludeAnnotation()
    {
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Exclude([]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeWithExcludeAnnotationOnlySerialize()
    {
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Exclude(['deserialize' => false]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeWithExcludeAnnotationOnlyDeserialize()
    {
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Exclude(['serialize' => false]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithExposeAnnotation()
    {
        $this->excluder->setRequireExpose(true);

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Expose([]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithExposeAnnotationOnlySerialize()
    {
        $this->excluder->setRequireExpose(true);

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Expose(['deserialize' => false]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithExposeAnnotationOnlyDeserialize()
    {
        $this->excluder->setRequireExpose(true);

        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Expose(['serialize' => false]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testDoNotExcludeWithExposeAnnotationWithoutRequireExpose()
    {
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Expose(['serialize' => false]), AnnotationSet::TYPE_PROPERTY);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            $annotations,
            false
        );

        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertFalse($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeWithoutExposeAnnotationWithRequireExpose()
    {
        $this->excluder->setRequireExpose(true);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, true));
        self::assertTrue($this->excluder->excludeProperty($propertyMetadata, false));
    }

    public function testExcludeFromStrategy()
    {
        $this->excluder->addExclusionStrategy(new BarPropertyExclusionStrategy(), true, true);
        $this->excluder->addExclusionStrategy(new FooPropertyExclusionStrategy(), true, true);

        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertTrue($this->excluder->excludePropertyByStrategy($propertyMetadata, true));
        self::assertTrue($this->excluder->excludePropertyByStrategy($propertyMetadata, false));
    }

    public function testExcludeFromStrategyFalse()
    {
        $propertyMetadata = new DefaultPropertyMetadata(
            'foo',
            'foo',
            new TypeToken('string'),
            ReflectionProperty::IS_PRIVATE,
            new DefaultClassMetadata(Foo::class, new AnnotationSet()),
            new AnnotationSet(),
            false
        );

        self::assertFalse($this->excluder->excludePropertyByStrategy($propertyMetadata, true));
        self::assertFalse($this->excluder->excludePropertyByStrategy($propertyMetadata, false));
    }
}
