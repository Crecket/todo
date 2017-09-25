<?php

namespace Greg\ToDo\Controllers;

class HomeController extends Controller
{
    /**
     * @param \Twig_Environment $twig
     * @return string
     */
    public function index(\Twig_Environment $twig)
    {
        return $twig->render("index.twig");
    }

}