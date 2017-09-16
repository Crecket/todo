<?php

namespace Greg\ToDo;

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
     * @param string $url
     * @param string $method
     * @return bool|mixed
     */
    public function isMatch(string $url, string $method)
    {
        return $url === $this->url && in_array($method, $this->methods);
    }

    /**
     * @param \Twig_Environment $twig
     * @return mixed
     */
    public function run(\Twig_Environment $twig)
    {
        if (is_callable($this->callback)) {
            return call_user_func(
                $this->callback,
                $this->url,
                $twig
            );
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