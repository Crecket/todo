<?php

namespace Greg\ToDo\Authentication;

use Greg\ToDo\Config;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ConfigItemNotFoundException;

class ProviderHandler
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
        $this->userModel = $this->config->get("security.authentication.user_model", true);
        if (!class_exists($this->userModel)) {
            throw new ClassNotFoundException("The configured user_model class does not exist");
        }

        $providers = $this->config->get("security.authentication.providers");
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

        var_dump($className);
        exit;
    }
}