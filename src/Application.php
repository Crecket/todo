<?php

namespace Greg\ToDo;

use Greg\ToDo\Authentication\ProviderHandler;
use Greg\ToDo\Console\Commands\UpdateSchemaCommand;
use Greg\ToDo\Console\ConsoleHandler;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\Http\BadRequestException;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Exceptions\Http\PermissionDeniedException;
use Greg\ToDo\Http\Request;
use Greg\ToDo\Http\Response;
use Greg\ToDo\Http\Router;

class Application
{
    /** @var bool $consoleMode */
    private $consoleMode = false;
    /** @var ConsoleHandler $consoleHandler */
    private $consoleHandler;
    /** @var Router $router */
    private $router;
    /** @var Config $config */
    private $config;
    /** @var Container $container */
    private $container;
    /** @var ProviderHandler $providerHandler */
    private $providerHandler;

    /**
     * Application constructor.
     * @param bool $consoleMode
     */
    public function __construct(bool $consoleMode = false)
    {
        $this->consoleMode = $consoleMode;

        $this->config = $this->loadConfig();
        $this->container = new Container($this->config);

        if ($this->consoleMode) {
            $this->consoleHandler = $this->registerConsoleCommands();
        }

        // register authentication providers and routes
        $this->router = $this->registerRoutes();
        $this->providerHandler = $this->registerAuthenticationProviders();
    }

    /**
     * @return string
     */
    public function run()
    {
        if ($this->consoleMode) {
            return $this->consoleHandler->run();
        }

        // only start session when we're running in http mode
        session_start();

        // run the registered authentication providers
        $this->providerHandler->checkProviders($this->router->getRequest());

        /** @var Response $response */
        $response = $this->router->run();
        return $response->output();
    }

    /**
     * @param string $configFile
     * @return array|mixed
     */
    private function loadConfig(string $configFile = 'config.yaml'): Config
    {
        $configLoader = new ConfigLoader(__DIR__."/Resources/");
        return $configLoader->load($configFile);
    }

    /**
     * @return Router
     */
    private function registerRoutes(): Router
    {
        $router = new Router($this->container);

        $routing = $this->config->get("routing");
        $routes = $routing['routes'] ?? [];
        $exceptions = $routing['exceptions'] ?? [];
        $middlewares = $routing['middlewares'] ?? [];

        // register the routes
        foreach ($routes as $route) {
            $router->register(
                $route['url'],
                (array)$route['method'],
                $route['callback'],
                (array)($route['middleware'] ?? [])
            );
        }

        // register the middlewares
        foreach ($middlewares as $middlewareKey => $middlewareClass) {
            $router->middleware($middlewareKey, $middlewareClass);
        }

        // register the exceptions
        foreach ($exceptions as $exception) {
            $exceptionHandler = $router->error($exception['exception'], $exception['callback']);
            // check if strict mode was set
            $exceptionHandler->setStrictMode($exception['strict'] ?? false);
        }

        /* PLACE HARDCODED ROUTES HERE */

        return $router;
    }

    /**
     * @return ConsoleHandler
     */
    private function registerConsoleCommands(): ConsoleHandler
    {
        $consoleHandler = new ConsoleHandler($this->container);

        $consoleHandler->register(UpdateSchemaCommand::class);

        return $consoleHandler;
    }

    /**
     * @return ProviderHandler
     */
    private function registerAuthenticationProviders(): ProviderHandler
    {
        $registration = new ProviderHandler($this->container);
        return $registration;
    }

    /**
     * @return bool
     */
    public function isConsoleMode(): bool
    {
        return $this->consoleMode;
    }

    /**
     * @return ConsoleHandler
     */
    public function getConsoleHandler(): ConsoleHandler
    {
        return $this->consoleHandler;
    }

    /**
     * @return Router
     */
    public function getRouter(): Router
    {
        return $this->router;
    }

    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return $this->config;
    }

    /**
     * @return Container
     */
    public function getContainer(): Container
    {
        return $this->container;
    }

    /**
     * @return ProviderHandler
     */
    public function getProviderHandler(): ProviderHandler
    {
        return $this->providerHandler;
    }
}