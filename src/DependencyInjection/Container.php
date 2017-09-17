<?php

namespace Greg\ToDo\DependencyInjection;

use Greg\ToDo\Config;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ServiceNotFoundException;

class Container
{
    /** @var Config $config */
    private $config;

    /**
     * Container constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $id
     * @return mixed
     * @throws ClassNotFoundException
     * @throws ServiceNotFoundException
     */
    public function get(string $id)
    {
        $service = $this->config->getService($id);

        $parameterValues = [];

        if (!empty($service['parameters'])) {
            $parameters = $service['parameters'];
            $parameterValues = $this->getParameterValues($parameters);
        }

        if (!class_exists($service['class'])) {
            throw new ClassNotFoundException();
        }

        // create the object instance and unpack parameter values in constructor
        return new $service['class'](...$parameterValues);
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function getParameterValues(array $parameters)
    {
        $parameterValues = [];
        foreach ($parameters as $parameter) {
            $parameterValues[$parameter] = $this->config->getParameter($parameter);
        }
        return $parameterValues;
    }
}
