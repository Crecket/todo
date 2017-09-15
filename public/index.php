<?php
require(__DIR__."/../src/Resources/config.php");
require(__DIR__."/../vendor/autoload.php");

use \Greg\ToDo\Repositories\ToDoRepository;
use \Greg\ToDo\Models\ToDo;

$factory = new \Greg\ToDo\Factories\RepositoryFactory();
/** @var ToDoRepository $repository */
$repository = $factory->get("ToDoRepository");
/** @var ToDo|bool $result */
$result = $repository->find(1);

var_dump($result);  