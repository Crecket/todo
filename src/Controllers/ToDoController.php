<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Exceptions\Http\PageNotFoundException;
use Greg\ToDo\FileHandler;
use Greg\ToDo\Http\Redirect;
use Greg\ToDo\Http\Request;
use Greg\ToDo\Models\File;
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
     * @return Redirect
     */
    public function add()
    {
        $todo = new ToDo();
        $todo->title = $_POST['title'];
        $todo->user_id = $_POST['user_id'];
        $todo->when = $_POST['when'];


        if (!empty($_FILES['uploaded_file'])) {
            /** @var FileHandler $fileHandler */
            $fileHandler = $this->container->get("application.file_handler");

            /** @var File $file */
            $file = $fileHandler->storeFileUpload("uploaded_file");

            // set the file hash
            $todo->file = $file->file_hash;
        }

        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $repository->insert($todo);

        return new Redirect("/home");
    }

    /**
     * @return Redirect
     */
    public function delete()
    {
        $todo = new ToDo();
        $todo->id = $_POST['id'];

        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $repository->delete($todo);

        return new Redirect("/home");
    }

    /**
     * @return Redirect
     */
    public function update()
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $todo = $repository->find($_POST['id']);
        if (!$todo instanceof ToDo) {
            // TODO error messages
            return new Redirect("/home");
        }

        $todo->title = $_POST['title'];
        $todo->user_id = $_POST['user_id'];
        $todo->when = $_POST['when'];

        $repository->update($todo);

        return new Redirect("/home");
    }

    /**
     * @return Redirect
     */
    public function complete()
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $todo = $repository->find($_POST['id']);
        if (!$todo instanceof ToDo) {
            // TODO error messages
            return new Redirect("/home");
        }
        $todo->completed = (int)$_POST['completed'];

        $repository->update($todo);
        return new Redirect("/home");
    }

    public function downloadTodoFile(\Twig_Environment $twig, Request $request)
    {
        /** @var ToDoRepository $todoRepository */
        $todoRepository = $this->container->get("repositories.todo_repository");

        $todo = $todoRepository->find($request->getParameter("todo_id"));

        if (!$todo instanceof ToDo) {
            // TODO error messages
            return new Redirect("/home");
        }

        // check if this item has a file store
        if (empty($todo->file)) {
            throw new PageNotFoundException();
        }

        /** @var FileHandler $fileHandler */
        $fileHandler = $this->container->get("application.file_handler");

        return $fileHandler->outputFile($todo->file);
    }
}