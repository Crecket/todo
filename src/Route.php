<?php

namespace Greg\ToDo;

class Route
{
    /** @var string $route */
    public $route;
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
    public function match(string $url)
    {
        if (!$url === $this->route) {
            return false;
        }
        if (is_callable($this->callback)) {
            return call_user_func($this->callback, $url);
        }

        $callbackSegments = explode(":", $this->callback);

        // create the controller instance using the class string
        $controller = new $callbackSegments[0];

        // call the method for the controller object
        return call_user_func_array(array($controller, $callbackSegments[1]), $url);
    }
}