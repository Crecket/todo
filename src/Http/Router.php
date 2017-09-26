<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\Config;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Http\Middleware\Middleware;
use Greg\ToDo\Http\Middleware\MiddlewareInterface;

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
    /** @var Middleware[] */
    private $middleware = [];
    /** @var array[] */
    private $middlewareMap = [];

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
     * @return Middleware
     * @throws ClassNotFoundException
     */
    public function middleware(string $name, string $class): Middleware
    {
        if (!class_exists($class)) {
            throw new ClassNotFoundException();
        }

        // create the middleware instance
        $this->middleware[$name] = new $class($this->container);

        // check if the middleware is of the correct type
        if (!$this->middleware[$name] instanceof MiddlewareInterface) {
            throw new ClassNotFoundException();
        }

        return $this->middleware[$name];
    }

    /**
     * @param string $route
     * @param array $methods
     * @param $callback
     * @param array $middlewares
     * @return Route|string
     * @throws ClassNotFoundException
     */
    public function register(string $route, array $methods, $callback, array $middlewares = [])
    {
        $routeKey = $this->uniqueRouteKey($route, $methods);
        $route = new Route($this->container, $route, $methods, $callback);

        // add the middlewares this route needs to the route
        $routeMiddleware = [];
        foreach ($middlewares as $middleware) {
            if ($this->middleware[$middleware]) {
                $routeMiddleware[] = $middleware;
            } else {
                throw new ClassNotFoundException("Middleware was not found/registered");
            }
        }

        // store the route in the router
        $this->routes[$routeKey] = $route;
        $this->middlewareMap[$routeKey] = $routeMiddleware;

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
            foreach ($this->routes as $routeKey => $route) {
                $routeMatcher = new RouteMatcher($this->request);

                // check if this route matches the url and method
                $routeMatchResult = $route->isMatch($routeMatcher);
                if ($routeMatchResult === false) {
                    continue;
                }

                // set the request parameters
                if (is_array($routeMatchResult)) {
                    $this->request->setParameters($routeMatchResult);
                }

                // run any middleware that this route needs
                foreach ($this->middlewareMap[$routeKey] as $middlewareKey) {
                    $response = $this->middleware[$middlewareKey]->run();

                    // check if we encountered a redirect
                    if ($response instanceof Redirect) {
                        $response->redirect();
                        exit; // prevent more actions from being taken
                    }
                }

                // run the route
                $response = $route->run($this->twig, $this->request);

                // check if we encountered a redirect
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