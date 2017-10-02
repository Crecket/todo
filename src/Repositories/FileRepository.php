<?php

namespace Greg\ToDo\Repositories;

use Greg\ToDo\Models\File;
use Greg\ToDo\ORM\Repository;

class FileRepository extends Repository
{
    const TABLE_NAME = "file";

    /** @var string $modelName */
    protected $modelName = File::class;
}