<?php

namespace Greg\ToDo\Console\Commands;

use Greg\ToDo\DependencyInjection\Container;

class ExportModelsCommand implements CommandInterface
{
    /** @var Container $container */
    private $container;

    /**
     * ExportModelsCommand constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return string
     */
    public function run(): string
    {


        return "Export ayyy";
    }

    /**
     * @return string
     */
    public function getCommandString(): string
    {
        return "orm:export";
    }
}