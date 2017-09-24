<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\Model;
use Greg\ToDo\Models\User;

class SessionAuthenticationProvider extends Provider
{
    /** @var array $options */
    private $options = [];
    /** @var bool $forceCheck */
    private $forceCheck = false;
    /** @var null|User $user */
    private $user;

    /**
     * @param array $options
     * @return bool
     */
    public function check(array $options): bool
    {
        $this->options = $options;
        $this->forceCheck = $options['force_check'] ?? false;

        if (empty($_SERVER['user'])) {
            return false;
        }



        return true;
    }

    public function getUser(): ?Model
    {
        // TODO: Implement getUser() method.
    }
}