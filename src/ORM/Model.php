<?php

namespace Greg\ToDo\ORM;

use Greg\ToDo\Exceptions\ORM\GetColumnsNotImplementedException;

abstract class Model implements ModelInterface
{
    public function primary()
    {
        return $this->id;
    }

    public function getRelations(): array
    {
        return [];
    }

    public function getColumns(): array
    {
        throw new GetColumnsNotImplementedException();
    }
}