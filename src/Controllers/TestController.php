<?php

namespace Greg\ToDo\Controllers;

class TestController
{
    /**
     * @param string $url
     * @param \Twig_Environment $twig
     * @throws \Exception
     */
    public static function test(string $url, \Twig_Environment $twig)
    {
        throw new \Exception("Just a test");
    }

}