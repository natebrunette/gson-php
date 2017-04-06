<?php
/*
 * Copyright (c) Nate Brunette.
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Tebru\Gson\Exception;

/**
 * Class UnexpectedJsonTokenException
 *
 * Thrown when an unexpected [@see JsonToken] is found
 *
 * @author Nate Brunette <n@tebru.net>
 */
class UnexpectedJsonTokenIteratorException extends UnexpectedJsonTokenException
{
    /**
     * Exceptions Indexed by Property/Key Name
     *
     * @var UnexpectedJsonTokenException[]
     */
    private $exceptions;

    /**
     * Constructor
     *
     * @param UnexpectedJsonTokenException[] $exceptions
     */
    public function __construct(array $exceptions)
    {
        $this->exceptions = $exceptions;
    }

    /**
     * Get Exceptions Indexed by Property/Key Name
     *
     * @return UnexpectedJsonTokenException[]
     */
    public function getExceptions()
    {
        return $this->exceptions;
    }

    /**
     * Get Exceptions in a Nested Tree Indexed by Property/Key Name
     *
     * @return array
     */
    public function getExceptionTree()
    {
        return array_map(
            function (UnexpectedJsonTokenException $exception) {
                if ($exception instanceof self) {
                    return $exception->getExceptionTree();
                }

                return $exception->getMessage();
            },
            $this->exceptions
        );
    }
}
