<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\Http\BadRequestException;
use Greg\ToDo\Exceptions\Http\FatalException;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Exceptions\Http\PermissionDeniedException;
use Greg\ToDo\Routing\Router;

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

        $router->error(PageNotFoundException::class, "ErrorController::error404");
        $router->error(PermissionDeniedException::class, "ErrorController::error403");
        $router->error(BadRequestException::class, "ErrorController::error400");
        $router->error(\Exception::class, "ErrorController::error500")->setStrictMode(false);

        $this->router = $router;
    }

}