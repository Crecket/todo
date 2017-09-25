<?php

namespace Greg\ToDo\Http\Middleware;

use Greg\ToDo\DependencyInjection\Container;

abstract class Middleware implements MiddlewareInterface
{
    /** @var Container $container */
    public $container;
    /** @var object|string $callback */
    public $callback;

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

    /**
     * @param \Twig_Environment $twig
     * @return mixed
     */
    public function run(\Twig_Environment $twig)
    {
        $callbackHandler = new CallbackHandler($this->container);
        return $callbackHandler->run($this->callback, array($twig));
    }
}