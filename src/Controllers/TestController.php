<?php

namespace Greg\ToDo\Controllers;

class TestController
{
    /**
     * @param \Twig_Environment $twig
     * @throws \Exception
     */
    public static function test(\Twig_Environment $twig)
    {
        throw new \Exception("Just a test");
    }

}