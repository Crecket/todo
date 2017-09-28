<?php

namespace Greg\ToDo\Models;

use Greg\ToDo\ORM\Model;

class User extends Model
{
    const TABLE_NAME = "user";

    /** @var integer $id */
    public $id;
    /** @var string $username */
    public $username;
    /** @var string $email */
    public $email;
    /** @var string $password */
    public $password;
    /** @var int $usergroup_id */
    public $usergroup_id;

    /**
     * @return int
     */
    public function primary()
    {
        return $this->id;
    }

    /**
     * @return array
     */
    public function getRelations(): array
    {
        return [
            'has_one' => [
                UserGroup::class => 'usergroup_id'
            ]
        ];
    }

    /**
     *
     */
    public function getColumns(): array
    {
        return [
            'id',
            'username',
            'email',
            'password',
            'usergroup_id'
        ];
    }
}