<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\DependencyInjection\Container;

class Middleware
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
     * @return mixed
     */
    public function run(\Twig_Environment $twig)
    {
        $callbackHandler = new CallbackHandler($this->container);
        return $callbackHandler->run($this->callback, array($twig));
    }
}