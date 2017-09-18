<?php

namespace Greg\ToDo\Authentication;

use Greg\ToDo\Config;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ConfigItemNotFoundException;

class ProviderRegistration
{
    /** @var Config $config */
    private $config;
    /** @var string $userModel */
    private $userModel;

    /**
     * ProviderRegistration constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
        $this->initialConfiguration();
    }

    private function initialConfiguration()
    {
        $this->userModel = $this->config->get("application.authentication.user_model", true);
        if (!class_exists($this->userModel)) {
            throw new ClassNotFoundException("The user_model Class set in the configuration was not found");
        }

        $this->setupProviders();
    }

    private function setupProviders()
    {
        $providers = $this->config->get("application.authentication.providers");
        foreach ($providers as $provider) {
            $this->setupProvider($provider);
        }
    }

    private function setupProvider($provider)
    {
        if (empty($provider['class'])) {
            throw new ConfigItemNotFoundException();
        }

        $className = $provider['class'];
        if (!class_exists($className)) {
            throw new ClassNotFoundException();
        }
    }
}