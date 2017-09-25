<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Config;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;

class Router
{
    /** @var Request $request */
    private $request;
    /** @var Route[] $routes */
    private $routes = [];
    /** @var ErrorHandler[] $errorHandlers */
    private $errorHandlers;
    /** @var \Twig_Environment $twig */
    private $twig;
    /** @var Container $container */
    private $container;
    /** @var string[] */
    private $middleware;

    /**
     * Router constructor.
     * @param Container $container
     * @param bool|Request $request
     */
    public function __construct(Container $container, $request = false)
    {
        $this->container = $container;
        $this->request = $request === false ? new Request() : $request;

        $loader = new \Twig_Loader_Filesystem(__DIR__.'/../Views');
        $this->twig = new \Twig_Environment($loader, array(
            "debug" => $this->container->getConfig()->get('application.debug')
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
     * @param string $name
     * @param string $class
     */
    public function middleware(string $name, string $class)
    {
        $middleware[$name] = $class;
    }

    /**
     * @param string $route
     * @param array $methods
     * @param $callback
     * @param array $middleware
     * @return Route|string
     */
    public function register(string $route, array $methods, $callback, array $middleware = [])
    {
        $routeKey = $this->uniqueRouteKey($route, $methods);
        $route = new Route($this->container, $route, $methods, $callback);

        $this->routes[$routeKey] = $route;

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
                $routeMatcher = new RouteMatcher($this->request);

                if (!$route->isMatch($routeMatcher)) {
                    continue;
                }
                $response = $route->run($this->twig);

                if ($response instanceof Redirect) {
                    $response->redirect();
                    exit; // prevent more actions from being taken
                }

                return $this->finish($response);
            }

            throw new PageNotFoundException();
        } catch (\Exception $exception) {
            /** @var ErrorHandler $errorHandler */
            foreach ($this->errorHandlers as $errorHandler) {

                if (!$errorHandler->isMatch($exception)) {
                    continue;
                }
                $response = $errorHandler->run($this->twig, $exception);

                if ($response instanceof Redirect) {
                    $response->redirect();
                    exit; // prevent more actions from being taken
                }

                return $this->finish($response);
            }

            // rethrow Exception if no matches are found
            throw $exception;
        }
    }

    private function registerMiddleware(array $middlewares)
    {
        foreach ($middlewares as $middleware) {

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
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @return Route[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @param string $url
     * @param array $methods
     * @return string
     */
    private function uniqueRouteKey(string $url, array $methods): string
    {
        return hash("sha256", $url.serialize($methods));
    }
}