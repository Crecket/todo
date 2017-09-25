<?php

namespace Greg\ToDo\Http\Middleware;

use Greg\ToDo\Exceptions\ClassNotFoundException;

class MiddlewareFactory implements MiddlewareInterface
{
    public static function create(string $middlewareName)
    {
        if (class_exists($middlewareName)) {
            /** @var Middleware $middlewareName */
            return new $middlewareName();
        }

        $defaultName = "\\Greg\\ToDO\\Http\\Middleware\\".$middlewareName;
        if (class_exists($defaultName)) {
            return new $defaultName;
        }

        throw new ClassNotFoundException();
    }
}