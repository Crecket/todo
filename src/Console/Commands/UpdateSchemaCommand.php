<?php

namespace Greg\ToDo\Console\Commands;

use Greg\ToDo\DependencyInjection\Container;

class UpdateSchemaCommand implements CommandInterface
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
     * @param array $arguments
     * @return string
     */
    public function run(array $arguments): string
    {
        return "Update schema not yet implemented";
    }

    /**
     * @return string
     */
    public function getCommandString(): string
    {
        return "orm:schema:update";
    }
}