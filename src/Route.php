<?php

namespace Greg\ToDo;

class Route
{
    /** @var string $route */
    public $url;
    /** @var string $method */
    public $method;
    /** @var object|string $callback */
    public $callback;

    /**
     * Route constructor.
     * @param string $url
     * @param string $method
     * @param object|string $callback
     */
    public function __construct(string $url, string $method = "GET", $callback)
    {
        $this->url = $url;
        $this->method = $method;
        $this->callback = $callback;
    }

    /**
     * @param string $url
     * @return bool|mixed
     */
    public function isMatch(string $url)
    {
        return $url === $this->url;
    }

    /**
     * @param \Twig_Environment $twig
     * @return mixed
     */
    public function run(\Twig_Environment $twig)
    {
        if (is_callable($this->callback)) {
            return call_user_func($this->callback, $this->url);
        }

        $callbackSegments = explode("::", $this->callback);
        $className = "Greg\\ToDo\\Controllers\\".$callbackSegments[0];
        $classMethod = $callbackSegments[1];

        // create the controller instance using the class string
        $controller = new $className;

        // call the method for the controller object
        return call_user_func_array(
            array($controller, $classMethod),
            array($this->url, $twig)
        );
    }
}