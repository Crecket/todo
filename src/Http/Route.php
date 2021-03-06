<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Http\Middleware\Middleware;

class Route
{
    /** @var Container $container */
    public $container;
    /** @var string $route */
    public $url;
    /** @var array $methods */
    public $methods;
    /** @var object|string $callback */
    public $callback;

    /**
     * Route constructor.
     * @param Container $container
     * @param string $url
     * @param array $methods
     * @param object|string $callback
     */
    public function __construct(Container $container, string $url, array $methods, $callback)
    {
        $this->container = $container;
        $this->url = $url;
        $this->methods = $methods;
        $this->callback = $callback;
    }

    /**
     * @param RouteMatcher $routeMatcher
     * @return bool|mixed
     */
    public function isMatch(RouteMatcher $routeMatcher)
    {
        return $routeMatcher->match($this->url, $this->methods);
    }

    /**
     * @param \Twig_Environment $twig
     * @param Request $request
     * @return mixed
     */
    public function run(\Twig_Environment $twig, Request $request)
    {
        $callbackHandler = new CallbackHandler($this->container);
        return $callbackHandler->run($this->callback, array($twig, $request));
    }
}