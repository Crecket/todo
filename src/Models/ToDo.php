<?php

namespace Greg\ToDo\Models;

class ToDo extends Model
{
    const TABLE_NAME = "todo";

    /** @var integer $id */
    public $id;
    /** @var string $title */
    public $title;
    /** @var string $responsible */
    public $responsible;
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
            ]
        ];
    }
}