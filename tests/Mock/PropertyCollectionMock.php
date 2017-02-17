<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Test\Mock;

use Tebru\Gson\Annotation as Gson;

/**
 * Class PropertyCollectionMock
 *
 * @author Nate Brunette <n@tebru.net>
 */
class PropertyCollectionMock
{
    /**
     * @Gson\Accessor(get="getChanged", set="setChanged")
     * @Gson\Type("bool")
     */
    private $changedAccessors;

    /**
     * @Gson\SerializedName("changedname")
     */
    public $changedName;

    /**
     * @Gson\Exclude()
     */
    private $exclude;

    /**
     * @Gson\Type("int")
     */
    private $type;

    public function getChanged()
    {
        return $this->changedAccessors;
    }

    public function setChanged($changed)
    {
        $this->changedAccessors = $changed;
    }

    /**
     * @Gson\VirtualProperty()
     * @Gson\SerializedName("new_virtual_property")
     * @Gson\Type("string")
     */
    public function virtualProperty()
    {
        return 'foo'.'bar';
    }

    /**
     * @Gson\VirtualProperty()
     * @Gson\Exclude
     */
    public function virtualProperty2()
    {
        return 'foo'.'bar';
    }
}
