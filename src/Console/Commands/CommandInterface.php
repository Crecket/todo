<?php

namespace Greg\ToDo\Console\Commands;

use Greg\ToDo\Console\ConsoleOutput;
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
     * @param ConsoleOutput $consoleOutput
     * @param array $arguments
     * @return string
     */
    public function run(ConsoleOutput $consoleOutput, array $arguments);
}