<?php

namespace Greg\ToDo\Routing;

class Route
{
    /** @var string $route */
    public $url;
    /** @var array $methods */
    public $methods;
    /** @var object|string $callback */
    public $callback;

    /**
     * Route constructor.
     * @param string $url
     * @param array $methods
     * @param object|string $callback
     */
    public function __construct(string $url, array $methods, $callback)
    {
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
        return CallbackHandler::run($this->callback, array($twig));
    }
}