<?php

namespace Greg\ToDo\Factories;

use Greg\ToDo\Database;
use Greg\ToDo\Exceptions\ClassNotFoundException;

class RepositoryFactory
{
    /**
     * RepositoryFactory constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param string $repository
     * @return mixed
     * @throws ClassNotFoundException
     */
    public function get(string $repository)
    {
        $className = "Greg\\ToDo\\Repositories\\".$repository;

        if(!class_exists($className)){
            throw new ClassNotFoundException();
        }

        return new $className(Database::$connection);
    }
}