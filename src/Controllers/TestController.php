<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Models\ToDo;
use Greg\ToDo\Models\ToDoComment;
use Greg\ToDo\Repositories\ToDoCommentRepository;
use Greg\ToDo\Repositories\ToDoRepository;

class TestController extends Controller
{

    public function test(\Twig_Environment $twig)
    {
        /** @var ToDoRepository $repository */
        $repository = $this->container->get("repositories.todo_repository");

        $todos = $repository->all();
        if (count($todos) === 0) {
            throw new \Exception("No todos found to test with");
        }
        $todo = $todos[0];

        $todoCommentsFinal = $repository->hasMany($todo, ToDoComment::class);

        /** @var ToDoCommentRepository $repository */
        $repository = $this->container->get("repositories.todo_comment_repository");

        $todocomments = $repository->all();
        if (count($todocomments) === 0) {
            throw new \Exception("No todos found to test with");
        }
        $todocomment = $todocomments[0];

        $todosFinal = $repository->belongsTo($todocomment, ToDo::class);

        var_dump(array(
            $todoCommentsFinal,
            $todosFinal
        ));
        return null;
    }

}