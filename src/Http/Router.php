<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Exceptions\Http\PageNotFoundException;

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
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Views');
        $this->twig = new \Twig_Environment($loader, array(
            "debug" => DEBUG
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    /**
     * @param string $route
     * @param array $methods
     * @param callable|string $callback
     * @return Route|string
     */
    public function add(string $route, array $methods, $callback)
    {
        return $this->register($route, $methods, $callback);
    }

    /**
     * @param string $route
     * @param callable|string $callback
     * @return Route|string
     */
    public function get(string $route, $callback)
    {
        return $this->register($route, array("GET"), $callback);
    }

    /**
     * @param string $route
     * @param callable|string $callback
     * @return Route|string
     */
    public function post(string $route, $callback)
    {
        return $this->register($route, array("POST"), $callback);
    }

    /**
     * @param string $exception
     * @param callable|string $callback
     * @return ErrorHandler
     */
    public function error(string $exception, $callback)
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

    /**
     * @param bool $url
     * @param bool $method
     * @return mixed
     * @throws \Exception
     */
    public function run($url = false, $method = false)
    {
        $url = $url === false ? $_SERVER['REQUEST_URI'] : $url;
        $method = $method === false ? $_SERVER['REQUEST_METHOD'] : $method;

        try {
            /** @var Route $route */
            foreach ($this->routes as $route) {
                $routeMatcher = new RouteMatcher($url, $method);
                if ($route->isMatch($routeMatcher)) {
                    $response = $route->run($this->twig);
                    return $this->finish($response);
                }
            }

            throw new PageNotFoundException();
        } catch (\Exception $exception) {
            /** @var ErrorHandler $errorHandler */
            foreach ($this->errorHandlers as $errorHandler) {
                if ($errorHandler->isMatch($exception)) {
                    $response = $errorHandler->run($this->twig, $exception);
                    return $this->finish($response);
                }
            }

            // rethrow Exception if no matches are found
            throw $exception;
        }
    }

    /**
     * @param Response|string $response
     * @return Response
     */
    private function finish($response)
    {
        if (!$response instanceof Response) {
            return new Response($response);
        }
        return $response;
    }
}