<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\PageNotFoundException;

class Router
{
    /** @var Route[] $routes */
    private $routes;
    /** @var array $errorHandlers */
    private $errorHandlers;
    /** @var \Twig_Environment $twig */
    private $twig;

    public function __construct()
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__.'/Views');
        $this->twig = new \Twig_Environment($loader, array(
            "debug" => DEBUG
        ));
        $this->twig->addExtension(new \Twig_Extension_Debug());
    }

    public function get(string $route, $callback)
    {
        $this->register($route, "GET", $callback);
    }

    public function post(string $route, $callback)
    {
        $this->register($route, "POST", $callback);
    }

    public function errorPage($code, $callback)
    {

    }

    private function register(string $route, string $method, $callback)
    {
        $this->routes[] = new Route($route, $method, $callback);
    }

    /**
     * @return string
     * @throws PageNotFoundException
     */
    public function run()
    {
        $url = $_SERVER["REQUEST_URI"];

        /** @var Route $route */
        foreach ($this->routes as $route) {
            if ($route->isMatch($url)) {
                return $route->run($this->twig);
            }
        }

        throw new PageNotFoundException();
    }
}