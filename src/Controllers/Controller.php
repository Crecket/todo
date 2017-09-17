<?php

namespace Greg\ToDo\Controllers;

use Greg\ToDo\DependencyInjection\Container;

abstract class Controller
{
    /** @var Container $container */
    protected $container;

    /**
     * Controller constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}