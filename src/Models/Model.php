<?php

namespace Greg\ToDo\Models;

abstract class Model implements ModelInterface
{
    public function getRelations(): array
    {
        return [];
    }
}