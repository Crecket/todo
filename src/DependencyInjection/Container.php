<?php

namespace Greg\ToDo\DependencyInjection;

use Greg\ToDo\Config;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ServiceNotFoundException;

class Container
{
    /** @var Config $config */
    private $config;
    /** @var array $singletons */
    private $singletons;

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
        $serviceInfo = $this->config->getService($id);
        $service = new Service($this->config, $this, $serviceInfo);

        if (!$service->isSingleton()) {
            return $service->createInstance();
        }

        if (!empty($this->singletons[$service->getClass()])) {
            return $this->singletons[$service->getClass()];
        }

        $instance = $service->createInstance();
        $this->singletons[$service->getClass()] = $instance;
        return $instance;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @param Config $config
     */
    public function setConfig(Config $config)
    {
        $this->config = $config;
    }
}
