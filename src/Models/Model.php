<?php

namespace Greg\ToDo\Models;

use Greg\ToDo\Exceptions\ORM\GetColumnsNotImplementedException;

abstract class Model implements ModelInterface
{
    public function getRelations(): array
    {
        return [];
    }

    public function getColumns(): array
    {
        throw new GetColumnsNotImplementedException();
    }
}