<?php

namespace Greg\ToDo\Models;

class UserGroup extends Model
{
    const TABLE_NAME = "usergroup";

    /** @var integer $id */
    public $id;
    /** @var string $name */
    public $name;
    /** @var int $authentication_level */
    public $authentication_level;

    /**
     * @return int
     */
    public function primary()
    {
        return $this->id;
    }
}