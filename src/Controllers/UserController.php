<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Http\Redirect;

class UserController extends Controller
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public function index(\Twig_Environment $twig)
    {
        return $twig->render("login.twig");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function login(\Twig_Environment $twig)
    {
        if (empty($_SESSION['user'])) {
            return new Redirect("/login");
        }

        return new Redirect("/home");
    }

    /**
     * @param \Twig_Environment $twig
     * @return Redirect
     */
    public function logout(\Twig_Environment $twig)
    {
        unset($_SESSION['user']);
        return new Redirect("/");
    }
}