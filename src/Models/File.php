<?php

namespace Greg\ToDo\Models;

use Greg\ToDo\ORM\Model;

class File extends Model
{
    const TABLE_NAME = "file";

    /** @var string $id */
    public $id;
    /** @var string $fileName */
    public $file_name;
    /** @var int $size */
    public $size;
    /** @var string $type */
    public $type;
    /** @var string $uploaded */
    public $uploaded;

    /**
     * @return int
     */
    public function primary()
    {
        return $this->id;
    }

    /**
     *
     */
    public function getColumns(): array
    {
        return [
            'id',
            'file_name',
            'size',
            'type',
            'uploaded'
        ];
    }
}