<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\ToDo;

class ToDoRepository extends Repository
{
    const TABLE_NAME = "todo";

    /** @var string $modelName */
    protected $modelName = ToDo::class;
}