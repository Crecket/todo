<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\Model;

interface AuthenticationProviderInterface
{
    public function check(array $options): bool;

    public function getUser(): ?Model;
}