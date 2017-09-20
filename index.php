<?php
require(__DIR__."/vendor/autoload.php");

$app = new \Greg\ToDo\Application(
    php_sapi_name() === "cli"
);
echo $app->run();
