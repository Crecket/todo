<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\PageNotFoundException;
use Greg\ToDo\Factories\RepositoryFactory;

class Application
{
    /** @var Router */
    private $router;

    /**
     * Application constructor.
     */
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
        $router->post("/add", "HomeController::add");
        $router->post("/delete", "HomeController::delete");
        $router->post("/update", "HomeController::update");

        $router->get("/test", "TestController::test");

        $router->error(PageNotFoundException::class, function ($twig, $exception) {
            return $twig->render("error404.twig");
        });

        $router->error(\Exception::class, function ($twig, $exception) {
            return $twig->render("error500.twig");
        })->setStrictMode(false);

        $this->router = $router;
    }

}