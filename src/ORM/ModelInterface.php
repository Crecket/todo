<?php

namespace Greg\ToDo\ORM;

interface ModelInterface
{
    public function primary();

    public function getRelations(): array;

    public function getColumns(): array;
}