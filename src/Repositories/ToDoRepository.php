<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\ToDo;
use Greg\ToDo\ORM\Repository;

class ToDoRepository extends Repository
{
    const TABLE_NAME = "todo";

    /** @var string $modelName */
    protected $modelName = ToDo::class;
}