<?php

namespace Greg\ToDo\Http\Middleware;

use Greg\ToDo\DependencyInjection\Container;

abstract class Middleware implements MiddlewareInterface
{
    /** @var Container $container */
    protected $container;
    /** @var object|string $callback */
    protected $callback;

    /**
     * Route constructor.
     * @param Container $container
     * @param object|string $callback
     */
    public function __construct(Container $container, $callback)
    {
        $this->container = $container;
        $this->callback = $callback;
    }
}