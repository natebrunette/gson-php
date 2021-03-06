Annotation Reference
====================

This page lists all of the annotations and ow to use them.

@Accessor
---------

Explicitly defines a getter or setter mapping.  Use the `get` or `set`
key to point to a method.

```php
/**
 * @Accessor(get="myCustomGetter", set="myCustomSetter")
 */
private $foo;
```

@Exclude
--------

Used to exclude a class or property from serialization or deserialization.

Exclude a property for both serialization and deserialization

```php
/**
 * @Exclude()
 */
private $foo
```

Exclude a class only during serialization

```php
/**
 * @Exclude(deserialize=false)
 */
class Foo {}
```

@Expose
-------

Only used in conjunction with `requireExposeAnnotation()` on the builder.
Can also be used for a single direction.

Expose a property

```php
/**
 * @Expose()
 */
private $foo
```

Exclude a class only during deserialization

```php
/**
 * @Expose(serialize=false)
 */
class Foo {}
```

@JsonAdapter
------------

Used to specify a custom Type Adapter, Type Adapter Factory, Json
Serializer, or Json Deserializer.  Can be used on either a class or a
property.

```php
/**
 * @JsonAdapter("My\Custom\ClassTypeAdapter")
 */
class Foo {
    /**
     * @JsonAdapter("My\Custom\ClassTypeAdapterFactory")
     */
    private $bar;

    /**
     * @JsonAdapter("My\Custom\ClassSerializer")
     */
    private $baz;

    /**
     * @JsonAdapter("My\Custom\ClassDeserializer")
     */
    private $qux;
}
```

@SerializedName
---------------

Allows overriding the property name that appears in JSON.

```php
/**
 * @SerializedName("bar")
 */
private $foo
```

@Since
------

Specifies when a property, class, or method was added.  If this number
is greater than or equals the current version, the property will be
excluded.

```php
/**
 * @Since("2.0")
 */
private $foo
```

@Type
-----

Overrides the type of a property.  If this annotation exist, the type
will be used before it is guessed.

```php
/**
 * @Type("DateTime")
 */
private $foo
```

Additional options can be passed into the type as well.  For example,
DateTime can use `format` and `timezone` options.

```php
/**
 * @Type("DateTime", options={format: "Y-m-d", timezone: "UTC"})
 */
private $foo
```

@Until
------

Specifies when a property, class, or method will be removed.  If this
number is less than the current version, the property will be excluded.

```php
/**
 * @Until("2.0")
 */
private $foo
```

@VirtualProperty
----------------

This acts as an identifier on methods.  It's purpose is to add
additional data during serialization.  It does nothing during
deserialization.  It can be used in conjunction with other annotations.

```php
/**
 * @VirtualProperty()
 * @SerializedName("foo")
 * @Type("int")
 */
public function getFoo()
{
    return $this->foo;
}
```
