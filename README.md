Gson PHP
========

[![Build Status](https://travis-ci.org/tebru/gson-php.svg?branch=master)](https://travis-ci.org/tebru/gson-php)
[![Code Coverage](https://scrutinizer-ci.com/g/tebru/gson-php/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/tebru/gson-php/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tebru/gson-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tebru/gson-php/?branch=master)

This library is a port of [google/gson](https://github.com/google/gson)
written in PHP.  Its purpose is to easily handle the conversion
between PHP objects and JSON.  It shares some of google/gson's goals
such as:

* A simple interface using `toJson` and `fromJson` methods
* Enable serialization and deserialization of 3rd party classes
* Allow schema differences between PHP objects and JSON

And in addition:

* Utilize PHP 7 scalar type hints to be intelligent about property types
* Limit annotation the number of annotations required to use

Overview
--------

Here are some simple use cases to get a feel for how the library works

If you have a PHP object you want to convert to JSON

```php
// $object obtained elsewhere

$gson = Gson::builder()->build();
$json = $gson->toJson($object);
```

What this is doing is using the provided `GsonBuilder` to set up the
`Gson` object with sensible defaults.  Calling `Gson::toJson` and
passing in an object will return a string of JSON.

The reverse is very similar

```php
// $json obtained elsewhere

$gson = Gson::builder()->build();
$fooObject = $gson->fromJson($json, Foo::class);
```

Now we call `Gson::fromJson` and pass in the json as a string and the type
of object we'd like to map to.  In this example, we will be getting
an instantiated `Foo` object back.

Documentation
-------------

* [Customizing Serialization/Deserialization](docs/CustomSerializers.md)
* [Excluding Classes and Properties](docs/Exclusion.md)
* [Customizing Class Instantiation](docs/InstanceCreator.md)
* [Types](docs/Types.md)
* [Json Element](docs/JsonElement.md)
* [Annotation Reference](docs/Annotations.md)
* [Advanced Usage](docs/Advanced.md)


Installation
------------

This library requires PHP 7.1

```bash
composer require tebru/gson-php
```

License
-------

This project is licensed under the MIT license. Please see the `LICENSE` file for more information.
