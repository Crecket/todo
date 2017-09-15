<?php

namespace Greg\ToDo\Factories;

use Greg\ToDo\Exceptions\ClassNotFound;

class RepositoryFactory
{
    /** @var \PDO $connection*/
    private $connection;

    /**
     * RepositoryFactory constructor.
     */
    public function __construct()
    {
        $dsn = 'mysql:host='.MYSQL_HOSTNAME.';dbname='.MYSQL_DATABASE;
        $this->connection = new \PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
    }

    /**
     * @param string $repository
     * @return mixed
     * @throws ClassNotFound
     */
    public function get(string $repository)
    {
        $className = "Greg\\ToDo\\Repositories\\".$repository;

        if(!class_exists($className)){
            throw new ClassNotFound();
        }

        return new $className($this->connection);
    }
}