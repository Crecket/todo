<?php

namespace Greg\ToDo;

use Greg\ToDo\Authentication\ProviderRegistration;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\Http\BadRequestException;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Exceptions\Http\PermissionDeniedException;
use Greg\ToDo\Http\Response;
use Greg\ToDo\Http\Router;

class Application
{
    /** @var Router $router */
    private $router;
    /** @var Config $config */
    private $config;
    /** @var Container $container */
    private $container;

    /**
     * Application constructor.
     */
    public function __construct()
    {
        $this->config = $this->loadConfig();

        $this->container = new Container($this->config);

        $this->router = $this->registerRoutes();

        $this->registerAuthenticationProviders();
    }

    /**
     * @return string
     */
    public function run()
    {
        /** @var Response $response */
        $response = $this->router->run();
        return $response->output();
    }

    /**
     * @param string $configFile
     * @return array|mixed
     */
    private function loadConfig(string $configFile = 'config.yaml')
    {
        $configLoader = new ConfigLoader(__DIR__."/Resources/");
        return $configLoader->load($configFile);
    }

    /**
     * @return Router
     */
    private function registerRoutes()
    {
        $router = new Router($this->container);

        $router->get("/", "ToDoController::home");
        $router->post("/add", "ToDoController::add");
        $router->post("/delete", "ToDoController::delete");
        $router->post("/update", "ToDoController::update");

        $router->get("/test", "TestController::test");

        $router->error(PageNotFoundException::class, "ErrorController::error404");
        $router->error(PermissionDeniedException::class, "ErrorController::error403");
        $router->error(BadRequestException::class, "ErrorController::error400");
        $router->error(\Exception::class, "ErrorController::error500")->setStrictMode(false);

        return $router;
    }

    private function registerAuthenticationProviders()
    {
        $registration = new ProviderRegistration($this->config);
    }

}