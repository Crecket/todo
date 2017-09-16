<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\Todo;

class ToDoRepository extends Repository
{
    const TABLE_NAME = "todo";

    /** @var string $modelName */
    protected $modelName = Todo::class;
}