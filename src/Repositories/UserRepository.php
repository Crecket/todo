<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\User;

class UserRepository extends Repository
{
    const TABLE_NAME = "user";

    /** @var string $modelName */
    protected $modelName = User::class;
}