<?php

namespace Greg\ToDo\Controllers;

class ToDoController extends Controller
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public function home(\Twig_Environment $twig)
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        /** @var ToDo[] $result */
        $todos = $repository->all();

        return $twig->render("home.twig", array(
            "todos" => $todos
        ));
    }

    public function add(\Twig_Environment $twig)
    {
    }

    public function delete(\Twig_Environment $twig)
    {
    }

    public function update(\Twig_Environment $twig)
    {
    }
}