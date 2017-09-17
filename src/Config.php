<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\ParameterNotFoundException;
use Greg\ToDo\Exceptions\ServiceNotFoundException;
use Symfony\Component\Yaml\Yaml;

class Config
{
    /**
     * @var array
     */
    private $config;

    /**
     * Config constructor.
     * @param array $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getParameters(){

    }

    public function getParameter(string $parameter, bool $strict = false)
    {
        if (empty($this->config['parameters'][$parameter])) {
            if ($strict) {
                throw new ServiceNotFoundException();
            }
            return null;
        }

        return $this->config['parameters'][$parameter];
    }

    public function getService(string $service)
    {
        if (empty($this->config['services'][$service])) {
            throw new ServiceNotFoundException();
        }

        return $this->config['services'][$service];
    }

    public function get(string $key, bool $strict = false)
    {
        if (!empty($this->config[$key])) {
            return $this->config[$key];
        }

        if ($strict) {
            throw new ParameterNotFoundException();
        }

        return null;
    }
}