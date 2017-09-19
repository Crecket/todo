<?php

namespace Greg\ToDo\Console\Commands;

use Greg\ToDo\DependencyInjection\Container;

interface CommandInterface
{
    /**
     * CommandInterface constructor.
     * @param Container $container
     */
    public function __construct(Container $container);

    /**
     * @return string
     */
    public function getCommandString(): string;

    /**
     * @return string
     */
    public function run(): string;
}