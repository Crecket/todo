<?php

namespace Greg\ToDo\DependencyInjection;

use Greg\ToDo\Config;
use Greg\ToDo\Exceptions\IncorrectTypeException;
use Greg\ToDo\Exceptions\InvalidParameterException;

class Service
{
    /** @var Config $config */
    private $config;
    /** @var Container $container */
    private $container;
    /** @var string $class */
    private $class;
    /** @var bool $singleton */
    private $singleton = false;
    /** @var array $parameters */
    private $parameters = [];

    /**
     * Service constructor.
     * @param Config $config
     * @param Container $container
     * @param array $service
     * @throws IncorrectTypeException
     */
    public function __construct(Config $config, Container $container, array $service)
    {
        $this->config = $config;
        $this->container = $container;
        $this->class = $service['class'];

        if (!class_exists($service['class'])) {
            throw new ClassNotFoundException();
        }

        if (!empty($service['parameters'])) {
            $this->parameters = $this->getParameterValues($service['parameters']);
        }

        if (!empty($service['singleton'])) {
            if (!is_bool($service['singleton'])) {
                throw new IncorrectTypeException();
            }

            $this->singleton = $service['singleton'];
        }
    }

    /**
     * @return mixed
     */
    public function createInstance()
    {
        return new $this->class(...array_values($this->parameters));
    }

    /**
     * @param array $parameters
     * @return array
     */
    private function getParameterValues(array $parameters)
    {
        $parameterValues = [];
        foreach ($parameters as $parameter) {
            $parameterValues[] = $this->parseParameter($parameter);
        }
        return $parameterValues;
    }

    /**
     * @param $parameter
     * @return mixed|null
     * @throws InvalidParameterException
     */
    private function parseParameter($parameter)
    {
        if (is_string($parameter)) {
            return $parameter;
        }

        if (is_array($parameter)) {
            switch ($parameter['type']) {
                case "service":
                    return $this->container->get($parameter['service']);
                    break;
                case "parameter":
                    return $this->config->getParameter($parameter['parameter']);
                    break;
                case "config_item":
                    return $this->config->get($parameter['config_item']);
                    break;
                case "config":
                    // simply returns the config instance
                    return $this->config;
                    break;
            }
        }

        throw new InvalidParameterException();
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->singleton;
    }

    /**
     * @param bool $singleton
     */
    public function setSingleton(bool $singleton)
    {
        $this->singleton = $singleton;
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }

}
