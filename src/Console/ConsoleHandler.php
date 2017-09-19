<?php

namespace Greg\ToDo\Console;

use Greg\ToDo\Console\Commands\CommandInterface;
use Greg\ToDo\DependencyInjection\Container;
use Greg\ToDo\Exceptions\ClassNotFoundException;
use Greg\ToDo\Exceptions\Console\InvalidConsoleCommandException;

class ConsoleHandler
{
    /** @var Container $container */
    private $container;
    /** @var array $options */
    private $options;
    /** @var string[] $classes */
    private $classes;
    /** @var string $command */
    private $command;

    /**
     * ConsoleHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->getOptions();
    }

    /**
     * @throws InvalidConsoleCommandException
     */
    private function getOptions()
    {
        $this->options = getopt("c:");
        if ($this->options === false) {
            throw new InvalidConsoleCommandException();
        }
        $this->command = $this->options['c'];
    }

    /**
     * @param string $commandClass
     * @throws ClassNotFoundException
     */
    public function register(string $commandClass)
    {
        if (!class_exists($commandClass)) {
            throw new ClassNotFoundException();
        }

        $this->classes[$commandClass] = $commandClass;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $output = "";

        foreach ($this->classes as $class) {
            /** @var CommandInterface $classInstance */
            $classInstance = $this->createInstance($class);

            if ($this->command === $classInstance->getCommandString()) {
                $output .= $classInstance->run();
                break;
            }
        }

        $output .= "\nSuccess\n";
        return $output;
    }

    /**
     * @param string $class
     * @return CommandInterface
     */
    private function createInstance(string $class): CommandInterface
    {
        return new $class($this->container);
    }

}