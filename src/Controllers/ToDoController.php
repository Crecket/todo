<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Factories\RepositoryFactory;

class ToDoController
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public static function home(\Twig_Environment $twig)
    {
        $factory = new RepositoryFactory();

        /** @var ToDoRepository $repository */
        $repository = $factory->get("ToDoRepository");

        /** @var ToDo[] $result */
        $todos = $repository->all();

        return $twig->render("home.twig", array(
            "todos" => $todos
        ));
    }

    public static function add(\Twig_Environment $twig)
    {
    }

    public static function delete(\Twig_Environment $twig)
    {
    }

    public static function update(\Twig_Environment $twig)
    {
    }
}