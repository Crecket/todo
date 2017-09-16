<?php

namespace Greg\ToDo\Models;

/**
 * @Annotation
 * @Target({"METHOD","PROPERTY"})
 */
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
}