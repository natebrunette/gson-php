<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Internal;

use ReflectionClass;
use Tebru\Gson\InstanceCreator;
use Tebru\Gson\Internal\ObjectConstructor\CreateFromInstanceCreator;
use Tebru\Gson\Internal\ObjectConstructor\CreateFromReflectionClass;
use Tebru\Gson\Internal\ObjectConstructor\CreateWithoutArguments;
use Tebru\Gson\PhpType;

/**
 * Class ConstructorConstructor
 *
 * This class acts as an ObjectConstructor factory.  It takes in a map of instance creators and
 * wraps object creation in an [@see ObjectConstructor].  This does expensive operations
 * (like reflection) once and allows it to be cached for subsequent calls.
 *
 * @author Nate Brunette <n@tebru.net>
 */
final class ConstructorConstructor
{
    /**
     * An array of [@see InstanceCreator] objects that can be used
     * for custom instantiation of a class
     *
     * @var InstanceCreator[]
     */
    private $instanceCreators;

    /**
     * Constructor
     *
     * @param InstanceCreator[] $instanceCreators
     */
    public function __construct(array $instanceCreators = [])
    {
        $this->instanceCreators = $instanceCreators;
    }

    /**
     * Get the correct [@see ObjectConstructor] to use
     *
     * @param PhpType $type
     * @return ObjectConstructor
     */
    public function get(PhpType $type)
    {
        $class = $type->getType();
        foreach ($this->instanceCreators as $instanceCreatorClass => $creator) {
            if ($type->isA($instanceCreatorClass)) {
                return new CreateFromInstanceCreator($creator, $type);
            }
        }

        $reflectionClass = new ReflectionClass($class);
        if (
            !$reflectionClass->isInstantiable()
            || (null !== $reflectionClass->getConstructor() && $reflectionClass->getConstructor()->getNumberOfRequiredParameters() > 0))
        {
            return new CreateFromReflectionClass($class);
        }

        return new CreateWithoutArguments($class);
    }
}
