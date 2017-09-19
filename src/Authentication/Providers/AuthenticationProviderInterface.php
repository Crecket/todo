<?php

namespace Greg\ToDo\Authentication\Providers;

interface AuthenticationProviderInterface
{
    public function setMatchConfig();

    public function match(string $url, string $method);
}