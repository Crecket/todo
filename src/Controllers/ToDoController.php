<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Http\Redirect;
use Greg\ToDo\Models\ToDo;
use Greg\ToDo\Models\User;
use Greg\ToDo\Repositories\ToDoRepository;
use Greg\ToDo\Repositories\UserRepository;

class ToDoController extends Controller
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public function home(\Twig_Environment $twig)
    {
        /** @var ToDoRepository $todoRepository */
        $todoRepository = $this->container->get("repositories.todo_repository");
        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("repositories.user_repository");

        /** @var ToDo[] $result */
        $todos = $todoRepository->all();

        /** @var User[] $result */
        $users = $userRepository->all();

        return $twig->render("home.twig", array(
            "todos" => $todos,
            "users" => $users
        ));
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function add(\Twig_Environment $twig)
    {
        $todo = new ToDo();
        $todo->title = $_POST['title'];
        $todo->user_id = $_POST['user_id'];
        $todo->when = $_POST['when'];

        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $repository->insert($todo);

        return new Redirect("/");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function delete(\Twig_Environment $twig)
    {
        $todo = new ToDo();
        $todo->id = $_POST['id'];

        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $repository->delete($todo);

        return new Redirect("/");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function update(\Twig_Environment $twig)
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $todo = $repository->find($_POST['id']);
        if (!$todo instanceof ToDo) {
            // TODO error messages
            return new Redirect("/");
        }

        $todo->title = $_POST['title'];
        $todo->user_id = $_POST['user_id'];
        $todo->when = $_POST['when'];

        $repository->update($todo);

        return new Redirect("/");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function complete(\Twig_Environment $twig)
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $todo = $repository->find($_POST['id']);
        if (!$todo instanceof ToDo) {
            // TODO error messages
            return new Redirect("/");
        }
        $todo->completed = (int)$_POST['completed'];

        $repository->update($todo);
        return new Redirect("/");
    }
}