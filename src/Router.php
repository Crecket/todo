<?php

namespace Greg\ToDo;

class Router
{
    /** @var array $routes */
    private $routes;

    public function get($route, $callback)
    {
        $this->register($route, "GET", $callback);
    }

    public function post($route, $callback)
    {
        $this->register($route, "POST", $callback);
    }

    public function errorPage($code, $callback){

    }

    private function register(string $route, string $method, $callback)
    {
        $this->routes[] = new Route($route, $method, $callback);
    }

    public function start()
    {
        $url = $_SERVER["REQUEST_URI"];

        /** @var Route $route */
        foreach($this->routes as $route){
            $routeResult = $route->match($url);
            if($routeResult !== false){
                return $routeResult;
            }
        }

        echo $re
    }
}