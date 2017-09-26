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
     * @return bool|array
     */
    public function match($url, $method)
    {
        $url = (array)$url;
        $method = (array)$method;

        if (!$this->matchMethod($method)) {
            return false;
        }

        $urlMatch = $this->matchUrl($url);
        if ($urlMatch !== false) {
            // return the parameter array
            return $urlMatch;
        }

        return false;
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

            $regexResult = $this->checkRegex($url);
            if ($regexResult !== false) {
                return $regexResult;
            }
        }
        return false;
    }

    /**
     * @param string $url
     * @return bool|array
     */
    private function checkRegex(string $url)
    {
        $targetUrlParts = explode("/", $url);
        $requestUrlParts = explode("/", $this->request->getUrl());

        if (count($targetUrlParts) !== count($requestUrlParts)) {
            return false;
        }

        $parameters = [];
        foreach ($targetUrlParts as $key => $targetUrlPart) {
            // check if its a direct match
            if ($targetUrlPart === $requestUrlParts[$key]) {
                continue;
            }

            // check if this part is a dynamic parameter
            $regexResult = preg_match("/^\(\:([0-9a-zA-Z_ ]*)\)$/", $targetUrlPart, $regexMatches);
            if ($regexResult === 1) {
                // store the value from the url into the correct parameter
                $parameters[$regexMatches[1]] = $requestUrlParts[$key];
                continue;
            }

            // urls didnt match and no parameter was found
            return false;
        }

        return $parameters;
    }
}