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
}