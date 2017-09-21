<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\ToDoComment;

class ToDoCommentRepository extends Repository
{
    const TABLE_NAME = "todo_comment";

    /** @var string $modelName */
    protected $modelName = ToDoComment::class;
}