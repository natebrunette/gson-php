<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock\ExclusionStrategies;

use Tebru\Gson\ClassMetadata;
use Tebru\Gson\ExclusionStrategy;
use Tebru\Gson\PropertyMetadata;

/**
 * Class BarPropertyExclusionStrategy
 *
 * @author Nate Brunette <n@tebru.net>
 */
class BarPropertyExclusionStrategy implements ExclusionStrategy
{
    /**
     * Return true if the class should be ignored
     *
     * @param ClassMetadata $classMetadata
     * @return bool
     */
    public function shouldSkipClass(ClassMetadata $classMetadata)
    {
        return false;
    }

    /**
     * Return true if the property should be ignored
     *
     * @param PropertyMetadata $propertyMetadata
     * @return bool
     */
    public function shouldSkipProperty(PropertyMetadata $propertyMetadata)
    {
        return 'bar' === $propertyMetadata->getName();
    }
}
