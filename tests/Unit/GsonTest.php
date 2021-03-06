<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */
namespace Tebru\Gson\Test\Unit;

use BadMethodCallException;
use DateTime;
use PHPUnit_Framework_TestCase;
use ReflectionProperty;
use Tebru\Gson\Gson;
use Tebru\Gson\Internal\Naming\UpperCaseMethodNamingStrategy;
use Tebru\Gson\Test\Mock\ChildClass;
use Tebru\Gson\Test\Mock\ExclusionStrategies\GsonMockExclusionStrategyMock;
use Tebru\Gson\Test\Mock\GsonObjectMock;
use Tebru\Gson\Test\Mock\GsonMock;
use Tebru\Gson\Test\Mock\GsonObjectMockInstanceCreatorMock;
use Tebru\Gson\Test\Mock\Strategy\TwoPropertyNamingStrategy;
use Tebru\Gson\Test\Mock\TypeAdapter\Integer1Deserializer;
use Tebru\Gson\Test\Mock\TypeAdapter\Integer1Serializer;
use Tebru\Gson\Test\Mock\TypeAdapter\Integer1SerializerDeserializer;
use Tebru\Gson\Test\Mock\TypeAdapter\Integer1TypeAdapter;
use Tebru\Gson\Test\Mock\TypeAdapter\Integer1TypeAdapterFactory;

/**
 * Class GsonTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Gson
 * @covers \Tebru\Gson\GsonBuilder
 */
class GsonTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleDeserialize()
    {
        $gson = Gson::builder()->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeNotSince()
    {
        $gson = Gson::builder()
            ->setVersion(1)
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame(null, $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeNotUntil()
    {
        $gson = Gson::builder()
            ->setVersion(2)
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame(null, $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeNoProtected()
    {
        $gson = Gson::builder()
            ->setExcludedModifier(ReflectionProperty::IS_PROTECTED)
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame(null, 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeRequireExpose()
    {
        $gson = Gson::builder()
            ->requireExposeAnnotation()
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(null, $gsonMock->getInteger());
        self::assertSame(null, $gsonMock->getFloat());
        self::assertSame(null, $gsonMock->getString());
        self::assertSame(null, $gsonMock->getBoolean());
        self::assertSame(null, $gsonMock->getArray());
        self::assertSame(null, $gsonMock->getDate());
        self::assertSame(null, $gsonMock->public);
        self::assertAttributeSame(null, 'protected', $gsonMock);
        self::assertSame(null, $gsonMock->getSince());
        self::assertSame(null, $gsonMock->getUntil());
        self::assertSame(null, $gsonMock->getMyAccessor());
        self::assertSame(null, $gsonMock->getSerializedname());
        self::assertSame(null, $gsonMock->getType());
        self::assertSame(null, $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(null, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(null, $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeCustomTypeAdapter()
    {
        $gson = Gson::builder()
            ->registerType('int', new Integer1TypeAdapter())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(2, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([2, 3, 4], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeCustomTypeAdapterFactory()
    {
        $gson = Gson::builder()
            ->addTypeAdapterFactory(new Integer1TypeAdapterFactory())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(2, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([2, 3, 4], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeCustomDeserializer()
    {
        $gson = Gson::builder()
            ->registerType(GsonMock::class, new Integer1Deserializer())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(2, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame(null, 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([2, 3, 4], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeCustomDeserializerBoth()
    {
        $gson = Gson::builder()
            ->registerType(GsonMock::class, new Integer1SerializerDeserializer())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(2, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame(null, 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([2, 3, 4], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeUsingInstanceCreator()
    {
        $gson = Gson::builder()
            ->addInstanceCreator(GsonObjectMock::class, new GsonObjectMockInstanceCreatorMock())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeWithExclusionStrategy()
    {
        $gson = Gson::builder()
            ->addExclusionStrategy(new GsonMockExclusionStrategyMock(), true, true)
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(null, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeWithPropertyNamingStrategy()
    {
        $gson = Gson::builder()
            ->setPropertyNamingStrategy(new TwoPropertyNamingStrategy())
            ->build();

        $array = [
            'integer2' => 1,
            'float2' => 3.2,
            'string2' => 'foo',
            'boolean2' => false,
            'array2' => ['foo' => 'bar'],
            'date2' => '2017-01-01T12:01:23-06:00',
            'public2' => 'public',
            'protected2' => 'protected',
            'since2' => 'since',
            'until2' => 'until',
            'accessor2' => 'accessor',
            'serialized_name' => 'serializedname',
            'type2' => [1, 2, 3],
            'jsonAdapter2' => 'bar',
            'expose2' => false,
            'exclude2' => true,
            'excludeFromStrategy2' => true,
            'gsonObjectMock2' => ['foo2' => 'bar'],
        ];

        $json = json_encode($array);

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($json, GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeWithMethodNamingStrategy()
    {
        $gson = Gson::builder()
            ->setMethodNamingStrategy(new UpperCaseMethodNamingStrategy())
            ->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), GsonMock::class);

        self::assertSame(1, $gsonMock->getInteger());
        self::assertSame(3.2, $gsonMock->getFloat());
        self::assertSame('foo', $gsonMock->getString());
        self::assertSame(false, $gsonMock->getBoolean());
        self::assertSame(['foo' => 'bar'], $gsonMock->getArray());
        self::assertSame('2017-01-01T12:01:23-06:00', $gsonMock->getDate()->format(DateTime::ATOM));
        self::assertSame('public', $gsonMock->public);
        self::assertAttributeSame('protected', 'protected', $gsonMock);
        self::assertSame('since', $gsonMock->getSince());
        self::assertSame('until', $gsonMock->getUntil());
        self::assertSame('accessor', $gsonMock->getMyAccessor());
        self::assertSame('serializedname', $gsonMock->getSerializedname());
        self::assertSame([1, 2, 3], $gsonMock->getType());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getJsonAdapter());
        self::assertSame(false, $gsonMock->getExpose());
        self::assertSame(null, $gsonMock->getExclude());
        self::assertSame(true, $gsonMock->getExcludeFromStrategy());
        self::assertEquals(new GsonObjectMock('bar'), $gsonMock->getGsonObjectMock());
    }

    public function testDeserializeUsesSameObject()
    {
        $gsonMock = new GsonMock();
        $gsonMock->setExclude(false);

        $gson = Gson::builder()->build();

        /** @var GsonMock $gsonMock */
        $gsonMock = $gson->fromJson($this->json(), $gsonMock);

        self::assertSame(false, $gsonMock->getExclude());
    }

    public function testSerializeSimple()
    {
        $gson = Gson::builder()->build();
        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 2;
        unset($json['exclude']);

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeNulls()
    {
        $gson = Gson::builder()
            ->serializeNull()
            ->build();
        $result = $gson->toJson(new GsonMock());
        
        $expected = '{
            "integer": null,
            "float": null,
            "string": null,
            "boolean": null,
            "array": null,
            "date": null,
            "public": null,
            "protected": null,
            "since": null,
            "until": null,
            "accessor": null,
            "serialized_name": null,
            "type": null,
            "json_adapter": null,
            "expose": null,
            "exclude_from_strategy": null,
            "gson_object_mock": null,
            "virtual": 2,
            "excluded_class": null
        }';

        self::assertJsonStringEqualsJsonString($expected, $result);
    }

    public function testSerializeNotSince()
    {
        $gson = Gson::builder()
            ->setVersion(1)
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 2;
        unset($json['exclude']);
        unset($json['since']);

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeNotUntil()
    {
        $gson = Gson::builder()
            ->setVersion(2)
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 2;
        unset($json['exclude']);
        unset($json['until']);

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeNotProtected()
    {
        $gson = Gson::builder()
            ->setExcludedModifier(ReflectionProperty::IS_PROTECTED)
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 2;
        unset($json['exclude']);
        unset($json['protected']);

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeRequireExpose()
    {
        $gson = Gson::builder()
            ->requireExposeAnnotation()
            ->build();

        $result = $gson->toJson($this->gsonMock());

        self::assertJsonStringEqualsJsonString('{"expose": false}', $result);
    }

    public function testSerializeCustomTypeAdapter()
    {
        $gson = Gson::builder()
            ->registerType('int', new Integer1TypeAdapter())
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        unset($json['exclude']);
        $json['virtual'] = 3;
        $json['integer'] = 2;
        $json['type'] = [2, 3, 4];

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeCustomTypeAdapterFactory()
    {
        $gson = Gson::builder()
            ->addTypeAdapterFactory(new Integer1TypeAdapterFactory())
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 3;
        unset($json['exclude']);
        $json['integer'] = 2;
        $json['type'] = [2, 3, 4];

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeCustomSerializer()
    {
        $gson = Gson::builder()
            ->registerType(GsonMock::class, new Integer1Serializer())
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        unset($json['exclude'], $json['protected']);
        $json['integer'] = 2;
        $json['type'] = [2, 3, 4];

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    public function testSerializeWithInvalidHandler()
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Handler of type "Tebru\Gson\Test\Mock\ChildClass" is not supported');

        Gson::builder()
            ->registerType('foo', new ChildClass())
            ->build();
    }

    public function testSerializeWithExclusionStrategy()
    {
        $gson = Gson::builder()
            ->addExclusionStrategy(new GsonMockExclusionStrategyMock(), true, true)
            ->build();

        $result = $gson->toJson($this->gsonMock());
        $json = json_decode($this->json(), true);
        $json['virtual'] = 2;
        unset($json['exclude']);
        unset($json['exclude_from_strategy']);

        self::assertJsonStringEqualsJsonString(json_encode($json), $result);
    }

    private function json(): string
    {
        $array = [
            'integer' => 1,
            'float' => 3.2,
            'string' => 'foo',
            'boolean' => false,
            'array' => ['foo' => 'bar'],
            'date' => '2017-01-01T12:01:23-06:00',
            'public' => 'public',
            'protected' => 'protected',
            'since' => 'since',
            'until' => 'until',
            'accessor' => 'accessor',
            'serialized_name' => 'serializedname',
            'type' => [1, 2, 3],
            'json_adapter' => 'bar',
            'expose' => false,
            'exclude' => true,
            'exclude_from_strategy' => true,
            'gson_object_mock' => ['foo' => 'bar'],
        ];

        return json_encode($array);
    }

    private function gsonMock(): GsonMock
    {
        $gsonMock = new GsonMock();
        $gsonMock->setInteger(1);
        $gsonMock->setFloat(3.2);
        $gsonMock->setString('foo');
        $gsonMock->setBoolean(false);
        $gsonMock->setArray(['foo' => 'bar']);
        $gsonMock->setDate(DateTime::createFromFormat(DateTime::ATOM, '2017-01-01T12:01:23-06:00'));
        $gsonMock->public = 'public';
        $gsonMock->setProtectedHidden('protected');
        $gsonMock->setSince('since');
        $gsonMock->setUntil('until');
        $gsonMock->setMyAccessor('accessor');
        $gsonMock->setSerializedname('serializedname');
        $gsonMock->setType([1, 2, 3]);
        $gsonMock->setJsonAdapter(new GsonObjectMock('bar'));
        $gsonMock->setExpose(false);
        $gsonMock->setExclude(true);
        $gsonMock->setExcludeFromStrategy(true);
        $gsonMock->setGsonObjectMock(new GsonObjectMock('bar'));

        return $gsonMock;
    }
}
