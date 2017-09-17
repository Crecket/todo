<?php

namespace Greg\ToDo;

class Database
{
    /** @var \PDO */
    public static $connection;

    /**
     * @param Config $config
     */
    public static function connect(Config $config)
    {
        $dsn = 'mysql:host='.$config->getParameter("database_host").';dbname='.$config->getParameter("database_name");
        self::$connection = new \PDO(
            $dsn,
            $config->getParameter("database_user"),
            $config->getParameter("database_password")
        );
    }
}