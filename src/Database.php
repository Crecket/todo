<?php

namespace Greg\ToDo;

class Database
{
    /** @var \PDO */
    public static $connection;

    public static function connect()
    {
        $dsn = 'mysql:host='.MYSQL_HOSTNAME.';dbname='.MYSQL_DATABASE;
        self::$connection = new \PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
    }
}