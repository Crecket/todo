<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Factories\RepositoryFactory;

class HomeController
{
    /**
     * @param string $url
     * @param \Twig_Environment $twig
     * @return string
     */
    public static function home(string $url, \Twig_Environment $twig)
    {

        $factory = new RepositoryFactory();

        /** @var ToDoRepository $repository */
        $repository = $factory->get("ToDoRepository");

        /** @var ToDo|bool $result */
        $todo = $repository->find(1);

        return $twig->render("home.twig", array(
            "todo" => $todo
        ));
    }
}