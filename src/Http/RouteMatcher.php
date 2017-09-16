<?php

namespace Greg\ToDo\Http;

class RouteMatcher
{
    /** @var string $url */
    private $url;
    /** @var string $method */
    private $method;

    /**
     * RouteMatcher constructor.
     * @param string $url
     * @param string $method
     */
    public function __construct(string $url, string $method)
    {
        $this->url = $url;
        $this->method = $method;
    }

    /**
     * @param $url
     * @param $methods
     * @return bool
     */
    public function match($url, $methods)
    {
        return $url === $this->url && in_array($this->method, $methods);
    }
}