<?php

namespace Greg\ToDo\Controllers;

class ErrorController
{
    public static function error404(\Twig_Environment $twig)
    {
        return $twig->render("errors/error404.twig");
    }

    public static function error500(\Twig_Environment $twig)
    {
        return $twig->render("errors/error500.twig");
    }

    public static function error403(\Twig_Environment $twig)
    {
        return $twig->render("errors/error403.twig");
    }

    public static function error400(\Twig_Environment $twig)
    {
        return $twig->render("errors/error400.twig");
    }
}