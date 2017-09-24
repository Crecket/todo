<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\DependencyInjection\Container;

abstract class Provider implements AuthenticationProviderInterface
{
    /** @var Container $container */
    protected $container;

    /**
     * Provider constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}