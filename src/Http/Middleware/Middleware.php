<?php

namespace Greg\ToDo\Http\Middleware;

use Greg\ToDo\DependencyInjection\Container;

abstract class Middleware implements MiddlewareInterface
{
    /** @var Container $container */
    protected $container;

    /**
     * Route constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}