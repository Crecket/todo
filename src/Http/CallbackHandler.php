<?php

namespace Greg\ToDo\Http;

use Greg\ToDo\DependencyInjection\Container;

class CallbackHandler
{
    /** @var Container $container */
    private $container;

    /**
     * CallbackHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @param $callback
     * @param array $parameters
     * @return mixed
     */
    public function run($callback, array $parameters)
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
        $controller = new $className($this->container);

        // call the method for the controller object
        return call_user_func_array(
            array($controller, $classMethod),
            $parameters
        );
    }
}