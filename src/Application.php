<?php

namespace Greg\ToDo;

use Greg\ToDo\Exceptions\Http\BadRequestException;
use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\Exceptions\Http\PermissionDeniedException;
use Greg\ToDo\Http\Response;
use Greg\ToDo\Http\Router;

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

        $this->router = $this->registerRoutes();
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
     * @return Router
     */
    private function registerRoutes()
    {
        $router = new Router();

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

}