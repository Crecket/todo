<?php

namespace Greg\ToDo\Models;

use Greg\ToDo\ORM\Model;

class ToDoComment extends Model
{
    const TABLE_NAME = "todo_comment";

    /** @var integer $id */
    public $id;
    /** @var string $text */
    public $text;
    /** @var string $name */
    public $name;
    /** @var \DateTime $when */
    public $when;
    /** @var int $todo_id */
    public $todo_id;

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
            'belongs_to' => [
                ToDo::class => 'id'
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
            'text',
            'name',
            'when',
            'todo_id'
        ];
    }
}