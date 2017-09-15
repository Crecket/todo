<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\Todo;

class ToDoRepository extends Repository
{
    /** @var string $modelName */
    protected $modelName = Todo::class;
    /** @var string $tableName */
    protected $tableName = "todo";
}