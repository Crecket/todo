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
    /** @var ConsoleOutput $consoleOutput */
    private $consoleOutput;
    /** @var array $options */
    private $options;
    /** @var string[] $classes */
    private $classes;
    /** @var string $command */
    private $command;
    /** @var array $arguments */
    private $arguments;

    /**
     * ConsoleHandler constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->consoleOutput = new ConsoleOutput();
        $this->getOptions();
    }

    /**
     * @throws InvalidConsoleCommandException
     */
    private function getOptions()
    {
        $this->options = $GLOBALS['argv'];

        if (!isset($this->options[1])) {
            throw new InvalidConsoleCommandException();
        }

        $this->command = $this->options[1];
        $this->arguments = array_splice($this->options, 2);
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
    public function run()
    {
        foreach ($this->classes as $class) {
            /** @var CommandInterface $classInstance */
            $classInstance = $this->createInstance($class);

            // check if command matches the command string
            if ($this->command === $classInstance->getCommandString()) {
                $classInstance->run($this->consoleOutput, $this->arguments);
                break;
            }
        }

        $this->consoleOutput->success("\nFinished command successfuly", false);
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