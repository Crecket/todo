<?php
require(__DIR__."/../src/Resources/config.php");
require(__DIR__."/../vendor/autoload.php");

$app = new \Greg\ToDo\Application();
echo $app->run();
