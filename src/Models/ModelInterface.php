<?php

namespace Greg\ToDo\Models;

interface ModelInterface
{
    public function primary();

    public function getRelations(): array;
}