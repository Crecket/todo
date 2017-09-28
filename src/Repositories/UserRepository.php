<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\User;
use Greg\ToDo\ORM\Repository;

class UserRepository extends Repository
{
    const TABLE_NAME = "user";

    /** @var string $modelName */
    protected $modelName = User::class;
}