<?php

namespace Greg\ToDo;

class Database
{
    /** @var \PDO */
    public $connection;
    /** @var string */
    private $hostname;
    /** @var int */
    private $port;
    /** @var string */
    private $database_name;
    /** @var string */
    private $username;
    /** @var string */
    private $password;

    /**
     * Database constructor.
     * @param Config $config
     * @param string $hostname
     * @param int $port
     * @param string $database_name
     * @param string $username
     * @param string $password
     */
    public function __construct(
        Config $config,
        string $hostname,
        int $port,
        string $database_name,
        string $username,
        string $password
    ) {
        $this->hostname = $hostname;
        $this->port = $port;
        $this->database_name = $database_name;
        $this->username = $username;
        $this->password = $password;

        $dsn = 'mysql:host='.$this->hostname.';dbname='.$this->database_name;

        try {
            $this->connection = new \PDO(
                $dsn,
                $this->username,
                $this->password
            );
        } catch (\Exception $ex) {
            if ($config->get("application.debug")) {
                var_dump($ex);
            }
        }
    }
}