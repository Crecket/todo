<?php

namespace Greg\ToDo\Models;

class ToDo implements ModelInterface
{
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

    public function getTable()
    {
        return [
            "id" => array("type" => "integer", "length" => 11, "null" => false),
            "title" => array("type" => "varchar", "null" => false),
            "responsible" => array("type" => "varchar", "null" => false),
            "when" => array("type" => "datetime", "null" => false),
            "added" => array("type" => "datetime", "null" => false)
        ];
    }
}