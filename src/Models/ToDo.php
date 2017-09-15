<?php

namespace Greg\ToDo\Models;

class ToDo
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
}