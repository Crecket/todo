<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\Model;
use Greg\ToDo\Models\User;
use Greg\ToDo\Repositories\UserRepository;

class SessionAuthenticationProvider extends Provider
{
    /** @var null|User $user */
    private $user;

    /**
     * @return bool
     */
    public function check(): bool
    {
        if (empty($_SERVER['user'])) {
            return false;
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("repositories.user_repository");

        $user = $userRepository->find($_SESSION['user']['id']);
        if (!$user instanceof User) {
            return false;
        }

        $_SESSION['user'] = $user;
        $this->user = $user;

        return true;
    }

    public function getUser(): ?Model
    {
        return $this->user;
    }
}