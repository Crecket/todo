<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\Http\Response;

class ErrorController extends Controller
{

    public function error500(\Twig_Environment $twig, \Exception $exception)
    {
        return new Response($twig->render("errors/error500.twig"), 500);
    }

    public function error404(\Twig_Environment $twig)
    {
        return new Response($twig->render("errors/error404.twig"), 404);
    }

    public function error403(\Twig_Environment $twig)
    {
        return new Response($twig->render("errors/error403.twig"), 403);
    }

    public function error400(\Twig_Environment $twig)
    {
        return new Response($twig->render("errors/error400.twig"), 400);
    }
}