<?php

namespace Greg\ToDo\Authentication;

use Greg\ToDo\Authentication\Providers\Provider;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\ConfigItemNotFoundException;
use Greg\ToDo\Exceptions\InvalidConfigurationException;
use Greg\ToDo\Http\Request;
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
     * @param Request $request
     * @return Provider|null
     * @throws InvalidConfigurationException
     */
    public function checkProviders(Request $request): ?Provider
    {
        foreach ($this->providers as $providerName => $provider) {
            if (empty($provider['match'])) {
                throw new InvalidConfigurationException("Missing required match property for authentication provider configuration");
            }
            if (empty($provider['match']['url'])) {
                throw new InvalidConfigurationException("Missing required match.url property for authentication provider configuration");
            }

            // default method to GET with no URLS
            $matchMethod = $provider['match']['method'] ?? array("GET");
            $matchUrl = (array)$provider['match']['url'] ?? [];

            // check if this request matches the urls and methods
            $routeMatcher = new RouteMatcher($request);
            if (!$routeMatcher->match($matchUrl, $matchMethod)) {
                continue;
            }

            // create a new instance for the provider and check the results
            /** @var Provider $providerInstance */
            $providerInstance = new $provider['class']($this->container);
            if (!$providerInstance->check()) {
                continue;
            }

            return $providerInstance;
        }
        return null;
    }

    /**
     * @throws ClassNotFoundException
     */
    private function initialConfiguration()
    {
        // set the user model name
        $this->userModel = $this->container->getConfig()->get("security.authentication.user_model", true);
        if (!class_exists($this->userModel)) {
            throw new ClassNotFoundException("The configured user_model class does not exist");
        }

        // get auth providers and loop through them
        $providers = (array)$this->container->getConfig()->get("security.authentication.providers");
        foreach ($providers as $providerName => $provider) {
            // loop through providers
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
        // check if provider has a class configured
        if (empty($provider['class'])) {
            throw new ConfigItemNotFoundException();
        }

        // check if the class exists
        if (!class_exists($provider['class'])) {
            throw new ClassNotFoundException();
        }

        $this->providers[$providerName] = $provider;
    }
}