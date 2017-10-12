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
        // get the service and create a new service object with our config and container
        $serviceInfo = $this->config->getService($id);
        $service = new Service($this->config, $this, $serviceInfo);

        // if this isn't a singleton we return a new instance
        if (!$service->isSingleton()) {
            return $service->createInstance();
        }

        // check if we already have a singleton instance and return it if we do
        if (!empty($this->singletons[$service->getClass()])) {
            return $this->singletons[$service->getClass()];
        }

        // create a new instance, store it in the list and return the instance
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
