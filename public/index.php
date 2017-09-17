<?php
require(__DIR__."/../vendor/autoload.php");

$app = new \Greg\ToDo\Application();
echo $app->run();