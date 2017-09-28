<?php

namespace Greg\ToDo\Models;

use Greg\ToDo\ORM\Model;

class ToDo extends Model
{
    const TABLE_NAME = "todo";

    /** @var integer $id */
    public $id;
    /** @var string $title */
    public $title;
    /** @var int $user_id */
    public $user_id;
    /** @var int $completed */
    public $completed;
    /** @var \DateTime $when */
    public $when;
    /** @var \DateTime $added */
    public $added;

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
            'has_many' => [
                ToDoComment::class => 'todo_id'
            ],
            'has_one' => [
                User::class => 'user_id'
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
            'title',
            'user_id',
            'completed',
            'when',
            'added'
        ];
    }
}