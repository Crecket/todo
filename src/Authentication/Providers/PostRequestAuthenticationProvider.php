<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\Models\User;
use Greg\ToDo\Repositories\UserRepository;

class PostRequestAuthenticationProvider extends Provider
{
    /**
     * @return bool
     */
    public function check(): bool
    {
        if (empty($_POST['username']) || empty($_POST['password'])) {
            return false;
        }

        /** @var UserRepository $userRepository */
        $userRepository = $this->container->get("repositories.user_repository");

        // find the user by username
        /** @var User $user */
        $user = $userRepository->findBy("username", $_POST['username'], true);
        if (!$user instanceof User) {
            return false;
        }

        if (!password_verify($_POST['password'], $user->password)) {
            return false;
        }

        $_SESSION['user'] = (array)$user;
        $this->user = $user;

        return true;
    }
}