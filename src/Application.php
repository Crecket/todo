<?php

namespace Greg\ToDo;

use Greg\ToDo\Factories\RepositoryFactory;

class Application
{
    /** @var Router */
    private $router;

    public function __construct()
    {
        Database::connect();

        $this->registerRoutes();
    }

    /**
     * @return string
     */
    public function run()
    {
        return $this->router->run();
    }

    /**
     *
     */
    private function registerRoutes()
    {
        $router = new Router();

        $router->get("/", "HomeController::home");

        $this->router = $router;
    }

}