<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Unit\Internal;

use ArrayIterator;
use PHPUnit_Framework_TestCase;
use SplStack;
use Tebru\Gson\Element\JsonArray;
use Tebru\Gson\Element\JsonNull;
use Tebru\Gson\Element\JsonObject;
use Tebru\Gson\Element\JsonPrimitive;
use Tebru\Gson\Exception\UnexpectedJsonTokenException;
use Tebru\Gson\Internal\JsonElementReader;
use Tebru\Gson\Internal\JsonObjectIterator;
use Tebru\Gson\Internal\TypeAdapter\JsonElementTypeAdapter;
use Tebru\Gson\JsonToken;

/**
 * Class JsonElementReaderTest
 *
 * @author Nate Brunette <n@tebru.net>
 * @covers \Tebru\Gson\Internal\JsonElementReader
 */
class JsonElementReaderTest extends PHPUnit_Framework_TestCase
{
    public function testBeginArray()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();

        $expected = new SplStack();
        $expected->push(new ArrayIterator([2]));

        $stack = $this->stack($reader);

        self::assertInstanceOf(ArrayIterator::class, $stack->top());
        self::assertSame(1, $stack->top()->current()->asInteger());
    }

    public function testBeginArrayInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "begin-array", but found "begin-object"');

        $reader = new JsonElementReader(new JsonObject());
        $reader->beginArray();
    }

    public function testEndArrayEmpty()
    {
        $reader = new JsonElementReader(new JsonArray());
        $reader->beginArray();
        $reader->endArray();

        self::assertAttributeCount(0, 'stack', $reader);
    }

    public function testEndArrayNonEmpty()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();
        $reader->nextInteger();
        $reader->endArray();

        self::assertAttributeCount(0, 'stack', $reader);
    }

    public function testEndArrayInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "end-array", but found "begin-object"');

        $jsonArray = new JsonArray();
        $jsonArray->addJsonElement(new JsonObject());
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();
        $reader->endArray();
    }

    public function testBeginObject()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();

        $stack = $this->stack($reader);

        self::assertInstanceOf(JsonObjectIterator::class, $stack->top());
        self::assertSame('key', $stack->top()->key());
    }

    public function testBeginObjectInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "begin-object", but found "begin-array"');

        $reader = new JsonElementReader(new JsonArray());
        $reader->beginObject();
    }

    public function testEndObjectEmpty()
    {
        $reader = new JsonElementReader(new JsonObject());
        $reader->beginObject();
        $reader->endObject();

        self::assertAttributeCount(0, 'stack', $reader);
    }

    public function testEndObjectNonEmpty()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();
        $reader->nextString();
        $reader->endObject();

        self::assertAttributeCount(0, 'stack', $reader);
    }

    public function testEndObjectInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "end-object", but found "name"');

        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->endObject();
    }

    public function testHasNextObjectTrue()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();

        self::assertTrue($reader->hasNext());
    }

    public function testHasNextObjectFalse()
    {
        $reader = new JsonElementReader(new JsonObject());
        $reader->beginObject();

        self::assertFalse($reader->hasNext());
    }

    public function testHasNextArrayTrue()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();

        self::assertTrue($reader->hasNext());
    }

    public function testHasNextArrayFalse()
    {
        $reader = new JsonElementReader(new JsonArray());
        $reader->beginArray();

        self::assertFalse($reader->hasNext());
    }

    public function testNextBooleanTrue()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(true));

        self::assertTrue($reader->nextBoolean());
    }

    public function testNextBooleanFalse()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(false));

        self::assertFalse($reader->nextBoolean());
    }

    public function testNextBooleanInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "boolean", but found "string"');

        $reader = new JsonElementReader(JsonPrimitive::create('test'));
        $reader->nextBoolean();
    }

    public function testNextDouble()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(1.1));

        self::assertSame(1.1, $reader->nextDouble());
    }

    public function testNextDoubleAsInt()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(1));

        self::assertSame(1.0, $reader->nextDouble());
    }

    public function testNextDoubleInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "number", but found "string"');

        $reader = new JsonElementReader(JsonPrimitive::create('1.1'));
        $reader->nextDouble();
    }

    public function testNextInteger()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(1));

        self::assertSame(1, $reader->nextInteger());
    }

    public function testNextIntegerInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "number", but found "string"');

        $reader = new JsonElementReader(JsonPrimitive::create('1'));
        $reader->nextInteger();
    }

    public function testNextString()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('test'));

        self::assertSame('test', $reader->nextString());
    }

    public function testNextStringIntType()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('1'));

        self::assertSame('1', $reader->nextString());
    }

    public function testNextStringDoubleType()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('1.1'));

        self::assertSame('1.1', $reader->nextString());
    }

    public function testNextStringBooleanTrueType()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('true'));

        self::assertSame('true', $reader->nextString());
    }

    public function testNextStringBooleanFalseType()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('false'));

        self::assertSame('false', $reader->nextString());
    }

    public function testNextStringNullType()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('null'));

        self::assertSame('null', $reader->nextString());
    }

    public function testNextStringIgnoresDoubleQuote()
    {
        $string = 'te"st';
        $reader = new JsonElementReader(JsonPrimitive::create($string));

        self::assertSame('te"st', $reader->nextString());
    }

    public function testNextStringIgnoresOtherTerminationCharacters()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('te]},st'));

        self::assertSame('te]},st', $reader->nextString());
    }

    public function testNextStringWithEscapedCharacters()
    {
        $string = 'te\\\/\b\f\n\r\t\u1234st';
        $reader = new JsonElementReader(JsonPrimitive::create($string));

        self::assertSame($string, $reader->nextString());
    }

    public function testNextStringWithEmoji()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('te👍st'));

        self::assertSame('te👍st', $reader->nextString());
    }

    public function testNextStringInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "string", but found "number"');

        $reader = new JsonElementReader(JsonPrimitive::create(1));
        $reader->nextString();
    }

    public function testNextStringName()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();

        self::assertSame('key', $reader->nextString());
    }

    public function testNextNull()
    {
        $reader = new JsonElementReader(new JsonNull());

        self::assertNull($reader->nextNull());
    }

    public function testNextNullInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "null", but found "string"');

        $reader = new JsonElementReader(JsonPrimitive::create('test'));
        $reader->nextNull();
    }

    public function testNextName()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();

        self::assertSame('key', $reader->nextName());
    }

    public function testNextNameInvalidToken()
    {
        $this->expectException(UnexpectedJsonTokenException::class);
        $this->expectExceptionMessage('Expected "name", but found "string"');

        $jsonObject = new JsonObject();
        $jsonObject->addString('key', 'value');
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();
        $reader->nextName();
    }

    public function testPeekEmptyArrayEnding()
    {
        $reader = new JsonElementReader(new JsonArray());
        $reader->beginArray();

        self::assertEquals(JsonToken::END_ARRAY, $reader->peek());
    }

    public function testPeekEmptyArrayDefault()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();

        self::assertEquals(JsonToken::NUMBER, $reader->peek());
    }

    public function testPeekNonEmptyArrayEnding()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();
        $reader->nextInteger();

        self::assertEquals(JsonToken::END_ARRAY, $reader->peek());
    }

    public function testPeekNonEmptyArrayNext()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addInteger(1);
        $jsonArray->addInteger(2);
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();
        $reader->nextInteger();

        self::assertEquals(JsonToken::NUMBER, $reader->peek());
    }

    public function testPeekNonEmptyObjectEnding()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addBoolean('key', true);
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();
        $reader->nextBoolean();

        self::assertEquals(JsonToken::END_OBJECT, $reader->peek());
    }

    public function testPeekNonEmptyObjectNext()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addBoolean('key', true);
        $jsonObject->addString('key2', false);
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();
        $reader->nextBoolean();

        self::assertEquals(JsonToken::NAME, $reader->peek());
    }

    public function testPeekEmptyObjectEnding()
    {
        $reader = new JsonElementReader(new JsonObject());
        $reader->beginObject();

        self::assertEquals(JsonToken::END_OBJECT, $reader->peek());
    }

    public function testPeekEmptyObjectName()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addBoolean('key', true);
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();

        self::assertEquals(JsonToken::NAME, $reader->peek());
    }

    public function testPeekDanglingName()
    {
        $jsonObject = new JsonObject();
        $jsonObject->addBoolean('key', true);
        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();

        self::assertEquals(JsonToken::BOOLEAN, $reader->peek());
    }

    public function testPeekEmptyDocumentBeginObject()
    {
        $reader = new JsonElementReader(new JsonObject());

        self::assertEquals(JsonToken::BEGIN_OBJECT, $reader->peek());
    }

    public function testPeekEmptyDocumentBeginArray()
    {
        $reader = new JsonElementReader(new JsonArray());

        self::assertEquals(JsonToken::BEGIN_ARRAY, $reader->peek());
    }

    public function testPeekEmptyDocument()
    {
        $reader = new JsonElementReader(new JsonArray());
        $reader->beginArray();
        $reader->endArray();

        self::assertEquals(JsonToken::END_DOCUMENT, $reader->peek());
    }

    public function testValueArray()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addJsonElement(new JsonArray());
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();

        self::assertEquals(JsonToken::BEGIN_ARRAY, $reader->peek());
    }

    public function testValueObject()
    {
        $jsonArray = new JsonArray();
        $jsonArray->addJsonElement(new JsonObject());
        $reader = new JsonElementReader($jsonArray);
        $reader->beginArray();

        self::assertEquals(JsonToken::BEGIN_OBJECT, $reader->peek());
    }

    public function testValueString()
    {
        $reader = new JsonElementReader(JsonPrimitive::create('test'));

        self::assertEquals(JsonToken::STRING, $reader->peek());
    }

    public function testValueTrue()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(true));

        self::assertEquals(JsonToken::BOOLEAN, $reader->peek());
    }

    public function testValueFalse()
    {
        $reader = new JsonElementReader(JsonPrimitive::create(false));

        self::assertEquals(JsonToken::BOOLEAN, $reader->peek());
    }

    public function testValueNull()
    {
        $reader = new JsonElementReader(new JsonNull());

        self::assertEquals(JsonToken::NULL, $reader->peek());
    }

    /**
     * @dataProvider provideValidNumbers
     */
    public function testValueNumber($number)
    {
        $reader = new JsonElementReader(JsonPrimitive::create((int)sprintf('%d', $number)));

        self::assertEquals(JsonToken::NUMBER, $reader->peek());
    }

    public function testSkipValue()
    {
        $array = [
            'skip' => [
                'prop1' => [
                    true,
                    false,
                    ['inner1' => 'innervalue'],
                ],
            ],
            'nextProp' => 1,
        ];
        $adapter = new JsonElementTypeAdapter();
        $jsonObject = $adapter->readFromJson(json_encode($array));

        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        $reader->nextName();
        $reader->skipValue();

        self::assertSame('nextProp', $reader->nextName());
    }

    public function testFormattedJson()
    {
        $string = '{  
           "id": 1,
           "addresses":[  
              {  
                 "city": "Bloomington",
                 "state": "MN",
                 "zip": 55431
              }
           ],
           "active": true
        }';

        $adapter = new JsonElementTypeAdapter();
        $jsonObject = $adapter->readFromJson($string);

        $reader = new JsonElementReader($jsonObject);
        $reader->beginObject();
        self::assertSame('id', $reader->nextName());
        self::assertSame(1, $reader->nextInteger());
        self::assertSame('addresses', $reader->nextName());
        $reader->beginArray();
        $reader->beginObject();
        self::assertSame('city', $reader->nextName());
        self::assertSame('Bloomington', $reader->nextString());
        self::assertSame('state', $reader->nextName());
        self::assertSame('MN', $reader->nextString());
        self::assertSame('zip', $reader->nextName());
        self::assertSame(55431, $reader->nextInteger());
        $reader->endObject();
        $reader->endArray();
        self::assertSame('active', $reader->nextName());
        self::assertTrue($reader->nextBoolean());
        $reader->endObject();
    }

    public function provideValidNumbers()
    {
        return [[0], [1], [2], [3], [4], [5], [6], [7], [8], [9], [-1]];
    }

    private function stack(JsonElementReader $reader): SplStack
    {
        return self::readAttribute($reader, 'stack');
    }
}
