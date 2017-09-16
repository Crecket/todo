<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\PageNotFoundException;

class Router
{
    /** @var Route[] $routes */
    private $routes;
    /** @var ErrorHandler[] $errorHandlers */
    private $errorHandlers;
    /** @var \Twig_Environment $twig */
    private $twig;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/Views');
        $this->twig = new \Twig_Environment($loader, array(
            "debug" => DEBUG
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    /**
     * @param string $route
     * @param array $methods
     * @param $callback
     * @return Route|string
     */
    public function add(string $route, array $methods, $callback)
    {
        return $this->register($route, $methods, $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return Route|string
     */
    public function get(string $route, $callback)
    {
        return $this->register($route, array("GET"), $callback);
    }

    /**
     * @param string $route
     * @param $callback
     * @return Route|string
     */
    public function post(string $route, $callback)
    {
        return $this->register($route, array("POST"), $callback);
    }

    public function error(string $exception, callable $callback)
    {
        $errorHandler = new ErrorHandler($exception, $callback);
        $this->errorHandlers[] = $errorHandler;
        return $errorHandler;
    }

    /**
     * @param string $route
     * @param array $methods
     * @param $callback
     * @return Route|string
     */
    private function register(string $route, array $methods, $callback)
    {
        $route = new Route($route, $methods, $callback);
        $this->routes[] = $route;
        return $route;
    }

    public function run()
    {
        $url = $_SERVER["REQUEST_URI"];
        $method = $_SERVER["REQUEST_METHOD"];

        try {
            /** @var Route $route */
            foreach ($this->routes as $route) {
                if ($route->isMatch($url, $method)) {
                    return $route->run($this->twig);
                }
            }

            throw new PageNotFoundException();
        } catch (\Exception $exception) {

            /** @var Route $route */
            foreach ($this->errorHandlers as $errorHandler) {
                if ($errorHandler->isMatch($exception)) {
                    return $errorHandler->run($this->twig, $exception);
                }
            }

            // rethrow Exception if no matches are found
            throw $exception;
        }
    }
}