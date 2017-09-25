<?php

namespace Greg\ToDo\Http;

class RouteMatcher
{
    /** @var Request $request */
    private $request;

    /**
     * RouteMatcher constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
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
            if ($method === $this->request->getMethod()) {
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
            if ($url === $this->request->getUrl()) {
                return true;
            }
        }
        return false;
    }
}