<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Unit\Internal;

use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Tebru\Gson\Annotation\Accessor;
use Tebru\Gson\Internal\AccessorMethodProvider;
use Tebru\Gson\Internal\Data\AnnotationSet;
use Tebru\Gson\Internal\Naming\UpperCaseMethodNamingStrategy;
use Tebru\Gson\Test\Mock\ChildClass;

/**
 * Class AccessorMethodProviderTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Internal\AccessorMethodProvider
 */
class AccessorMethodProviderTest extends PHPUnit_Framework_TestCase
{
    public function testGetterWithoutAnnotation()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('baz');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $method = $methodProvider->getterMethod($reflectionClass, $reflectionProperty, new AnnotationSet());

        self::assertSame('getBaz', $method->getName());
    }

    public function testGetterWithAnnotation()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('baz');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Accessor(['get' => 'baz']), AnnotationSet::TYPE_PROPERTY);
        $method = $methodProvider->getterMethod($reflectionClass, $reflectionProperty, $annotations);

        self::assertSame('baz', $method->getName());
    }

    public function testGetterNull()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('foo');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $method = $methodProvider->getterMethod($reflectionClass, $reflectionProperty, new AnnotationSet());

        self::assertNull($method);
    }

    public function testSetterWithoutAnnotation()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('baz');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $method = $methodProvider->setterMethod($reflectionClass, $reflectionProperty, new AnnotationSet());

        self::assertSame('setBaz', $method->getName());
    }

    public function testSetterWithAnnotation()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('baz');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $annotations = new AnnotationSet();
        $annotations->addAnnotation(new Accessor(['set' => 'set_baz']), AnnotationSet::TYPE_PROPERTY);
        $method = $methodProvider->setterMethod($reflectionClass, $reflectionProperty, $annotations);

        self::assertSame('set_baz', $method->getName());
    }

    public function testSetterNull()
    {
        $reflectionClass = new ReflectionClass(ChildClass::class);
        $reflectionProperty = $reflectionClass->getProperty('foo');
        $methodProvider = new AccessorMethodProvider(new UpperCaseMethodNamingStrategy());
        $method = $methodProvider->setterMethod($reflectionClass, $reflectionProperty, new AnnotationSet());

        self::assertNull($method);
    }
}
