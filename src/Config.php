<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\ConfigItemNotFoundException;
use Greg\ToDo\Exceptions\ParameterNotFoundException;
use Greg\ToDo\Exceptions\ServiceNotFoundException;

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

    /**
     * @param string $parameter
     * @param bool $strict
     * @return null
     * @throws ParameterNotFoundException
     */
    public function getParameter(string $parameter, bool $strict = false)
    {
        if (empty($this->config['parameters'][$parameter])) {
            if ($strict) {
                throw new ParameterNotFoundException("Parameter ".$parameter." is required and was not found");
            }
            return null;
        }

        return $this->config['parameters'][$parameter];
    }

    /**
     * @param string $service
     * @return mixed
     * @throws ServiceNotFoundException
     */
    public function getService(string $service)
    {
        if (empty($this->config['services'][$service])) {
            throw new ServiceNotFoundException("Service ".$service." is required and was not found");
        }

        return $this->config['services'][$service];
    }

    /**
     * @param string $key
     * @param bool $strict
     * @return mixed|null
     * @throws ConfigItemNotFoundException
     */
    public function get(string $key, bool $strict = false)
    {
        if (!empty($this->config[$key])) {
            return $this->config[$key];
        }

        $keyParts = explode(".", $key);
        $configScope = $this->config;

        if (count($keyParts) <= 1) {
            if ($strict) {
                throw new ConfigItemNotFoundException("Config item ".$key." is required and was not found");
            }
            return null;
        }

        foreach ($keyParts as $keyPart) {
            if (isset($configScope[$keyPart]) || is_null($configScope[$keyPart])) {
                $configScope = $configScope[$keyPart];
                continue;
            }

            if ($strict) {
                throw new ConfigItemNotFoundException("Config item ".$key." is required and was not found");
            }
            return null;
        }

        return $configScope;
    }
}