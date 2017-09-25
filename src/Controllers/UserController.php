<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Http\Redirect;
use Greg\ToDo\Models\User;
use Greg\ToDo\Repositories\UserRepository;

class UserController extends Controller
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public function home(\Twig_Environment $twig)
    {
        return $twig->render("login.twig");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function login(\Twig_Environment $twig)
    {
        /** @var UserRepository $repository */
        $repository = $this->container->get("repositories.user_repository");

        $user = $repository->findBy("username", $_POST['username']);
        if (!$user instanceof User) {
            return new Redirect("/");
        }

        return new Redirect("/");
    }
}