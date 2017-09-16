<?php

namespace Greg\ToDo\Routing;

class CallbackHandler
{
    /**
     * @param $callback
     * @param array $parameters
     * @return mixed
     */
    public static function run($callback, array $parameters)
    {
        if (is_callable($callback)) {
            return call_user_func_array(
                $callback,
                $parameters
            );
        }

        $callbackSegments = explode("::", $callback);
        $className = "Greg\\ToDo\\Controllers\\".$callbackSegments[0];
        $classMethod = $callbackSegments[1];

        // create the controller instance using the class string
        $controller = new $className;

        // call the method for the controller object
        return call_user_func_array(
            array($controller, $classMethod),
            $parameters
        );
    }
}