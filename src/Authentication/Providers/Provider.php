<?php

namespace Greg\ToDo\Authentication\Providers;

use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Models\Model;

abstract class Provider implements AuthenticationProviderInterface
{
    /** @var Container $container */
    protected $container;
    /** @var Model */
    protected $user;

    /**
     * Provider constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return Model|null
     */
    public function getUser(): ?Model
    {
        return $this->user;
    }
}