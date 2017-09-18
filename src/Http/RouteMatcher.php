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
     * @param string $url
     * @param array $methods
     * @return bool
     */
    public function match(string $url, array $methods)
    {
        return $this->matchUrl($url) && $this->matchMethod($methods);
    }

    /**
     * @param array $methods
     * @return bool
     */
    private function matchMethod(array $methods)
    {
        if ($this->method === "ANY") {
            return true;
        }
        return in_array($this->method, $methods);
    }

    private function matchUrl(string $url)
    {
        if ($url === "*") {
            return true;
        }
        return $url === $this->url;
    }
}