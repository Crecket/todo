<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\User;
use Greg\ToDo\ORM\Repository;

class UserGroupRepository extends Repository
{
    const TABLE_NAME = "usergroup";

    /** @var string $modelName */
    protected $modelName = User::class;
}