<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\Model;

class PostRequestAuthenticationProvider extends Provider
{
    public function check(): bool
    {
        // TODO: Implement check() method.
        return false;
    }

    public function getUser(): ?Model
    {
        // TODO: Implement getUser() method.
        return null;
    }
}