<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Annotation;

/**
 * Class VirtualProperty
 *
 * This allows a method to be used as a property during serialization only. This
 * is helpful if your serialized models need to contain extra properties.  For example,
 * an aggregate of two separate properties on the model.
 *
 * @author Nate Brunette <n@tebru.net>
 * @Annotation
 * @Target({"METHOD"})
 */
class VirtualProperty
{
}
