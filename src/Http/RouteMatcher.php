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
     * @param string|array $url
     * @param string|array $method
     * @return bool
     */
    public function match($url, $method)
    {
        if (!is_array($url)) {
            $url = array($url);
        }
        if (!is_array($method)) {
            $method = array($method);
        }
        return $this->matchUrl($url) && $this->matchMethod($method);
    }

    /**
     * @param array $methods
     * @return bool
     */
    private function matchMethod(array $methods)
    {
        foreach ($methods as $method) {
            if ($method === "ANY") {
                return true;
            }
            if ($method === $this->method) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param array $urls
     * @return bool
     */
    private function matchUrl(array $urls)
    {
        foreach ($urls as $url) {
            if ($url === "*") {
                return true;
            }
            if ($url === $this->url) {
                return true;
            }
        }
        return false;
    }
}