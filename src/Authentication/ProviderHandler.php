<?php

namespace Greg\ToDo\Authentication;

use Greg\ToDo\Authentication\Providers\AuthenticationProviderInterface;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ConfigItemNotFoundException;
use Greg\ToDo\Exceptions\InvalidConfigurationException;
use Greg\ToDo\Http\RouteMatcher;
use Greg\ToDo\Http\Router;

class ProviderHandler
{
    /** @var Container $container */
    private $container;
    /** @var string $userModel */
    private $userModel;
    /** @var array $providers */
    private $providers;

    /**
     * ProviderRegistration constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->initialConfiguration();
    }

    /**
     * @param Router $router
     * @throws InvalidConfigurationException
     */
    public function checkProviders(Router $router)
    {
        foreach ($this->providers as $providerName => $provider) {
            if (empty($provider['match'])) {
                throw new InvalidConfigurationException("Missing required match property for authentication provider configuration");
            }
            if (empty($provider['match']['url'])) {
                throw new InvalidConfigurationException("Missing required match.url property for authentication provider configuration");
            }

            // default method to GET
            $matchMethod = $provider['match']['method'] ?? array("GET");
            $matchUrl = (array)$provider['match']['url'] ?? [];

            $routeMatcher = new RouteMatcher($router->getUrl(), $router->getMethod());
            if (!$routeMatcher->match($matchUrl, $matchMethod)) {
                continue;
            }

            /** @var AuthenticationProviderInterface $providerInstance */
            $providerInstance = new $provider['class']($this->container);
            if (!$providerInstance->check((array)$provider['options'])) {
                continue;
            }
        }
    }

    /**
     * @throws ClassNotFoundException
     */
    private function initialConfiguration()
    {
        $this->userModel = $this->container->getConfig()->get("security.authentication.user_model", true);
        if (!class_exists($this->userModel)) {
            throw new ClassNotFoundException("The configured user_model class does not exist");
        }

        $providers = (array)$this->container->getConfig()->get("security.authentication.providers");
        foreach ($providers as $providerName => $provider) {
            $this->setupProvider($providerName, $provider);
        }
    }

    /**
     * @param string $providerName
     * @param array $provider
     * @throws ClassNotFoundException
     * @throws ConfigItemNotFoundException
     */
    private function setupProvider(string $providerName, array $provider)
    {
        if (empty($provider['class'])) {
            throw new ConfigItemNotFoundException();
        }

        $className = $provider['class'];

        if (!class_exists($className)) {
            throw new ClassNotFoundException();
        }

        $this->providers[$providerName] = $provider;
    }
}