<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\Model;
use Greg\ToDo\Models\User;
use Greg\ToDo\Repositories\UserRepository;

class SessionAuthenticationProvider extends Provider
{
    /**
     * @return bool
     */
    public function check(): bool
    {
        if (empty($_SESSION['user'])) {
            return false;
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("repositories.user_repository");

        // check if the user still exists
        /** @var User $user */
        $user = $userRepository->find($_SESSION['user']['id']);
        if (!$user instanceof User) {
            return false;
        }

        $_SESSION['user'] = (array)$user;
        $this->user = $user;

        return true;
    }
}