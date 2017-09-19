<?php

namespace Greg\ToDo\Models;

class User implements ModelInterface
{
    /** @var integer $id */
    public $id;
    /** @var string $username */
    public $username;
    /** @var string $email */
    public $email;
    /** @var string $password */
    public $password;

    /**
     * @return int
     */
    public function primary()
    {
        return $this->id;
    }

    public function getTable()
    {
        return [
            "id" => array("type" => "integer", "length" => 11, "ai" => true),
            "username" => array("type" => "varchar"),
            "email" => array("type" => "varchar"),
            "password" => array("type" => "varchar")
        ];
    }
}