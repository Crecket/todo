<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Config;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;

class Router
{
    /** @var string */
    private $url = "";
    /** @var string */
    private $method = "";
    /** @var Route[] $routes */
    private $routes = [];
    /** @var ErrorHandler[] $errorHandlers */
    private $errorHandlers;
    /** @var \Twig_Environment $twig */
    private $twig;
    /** @var Container $container */
    private $container;

    /**
     * Router constructor.
     * @param Container $container
     * @param bool $url
     * @param bool $method
     */
    public function __construct(Container $container, $url = false, $method = false)
    {
        $this->container = $container;

        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Views');
        $this->twig = new \Twig_Environment($loader, array(
            "debug" => $this->container->getConfig()->get('application.debug')
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());

        $this->url = $url === false ? ($_SERVER['REQUEST_URI'] ?? "") : $url;
        $this->method = $method === false ? ($_SERVER['REQUEST_METHOD'] ?? "") : $method;

        if (!empty($_POST['_method'])) {
            switch (strtoupper($_POST['_method'])) {
                case 'PUT':
                    $this->method = "PUT";
                    break;
                case 'DELETE':
                    $this->method = "DELETE";
                    break;
                default:
                    $this->method = "POST";
                    break;
            }
        }
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
     * @param string $route
     * @param callable|string $callback
     * @return Route|string
     */
    public function put(string $route, $callback)
    {
        return $this->register($route, array("PUT"), $callback);
    }

    /**
     * @param string $route
     * @param callable|string $callback
     * @return Route|string
     */
    public function delete(string $route, $callback)
    {
        return $this->register($route, array("DELETE"), $callback);
    }

    /**
     * @param string $exception
     * @param callable|string $callback
     * @return ErrorHandler
     */
    public function error(string $exception, $callback)
    {
        $errorHandler = new ErrorHandler($this->container, $exception, $callback);
        $this->errorHandlers[] = $errorHandler;
        return $errorHandler;
    }

    /**
     * @param string $route
     * @param array $methods
     * @param $callback
     * @return Route|string
     */
    public function register(string $route, array $methods, $callback)
    {
        $route = new Route($this->container, $route, $methods, $callback);
        $this->routes[] = $route;
        return $route;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function run()
    {
        try {
            /** @var Route $route */
            foreach ($this->routes as $route) {
                $routeMatcher = new RouteMatcher($this->url, $this->method);
                if ($route->isMatch($routeMatcher)) {
                    $response = $route->run($this->twig);

                    if ($response instanceof Redirect) {
                        $response->redirect();
                        exit;
                    }

                    return $this->finish($response);
                }
            }

            throw new PageNotFoundException();
        } catch (\Exception $exception) {
            /** @var ErrorHandler $errorHandler */
            foreach ($this->errorHandlers as $errorHandler) {
                if ($errorHandler->isMatch($exception)) {
                    $response = $errorHandler->run($this->twig, $exception);

                    if ($response instanceof Redirect) {
                        $response->redirect();
                        exit;
                    }

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

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

}