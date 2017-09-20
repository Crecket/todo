<?php

namespace Greg\ToDo\Console\Commands;

use Greg\ToDo\Console\ConsoleOutput;
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
     * @param ConsoleOutput $consoleOutput
     * @param array $arguments
     * @return string
     */
    public function run(ConsoleOutput $consoleOutput, array $arguments)
    {
        $consoleOutput->warning("Update schema not yet implemented");

        $progressMin = 0;
        $progressMax = 600;

        $progress = $consoleOutput->progress($progressMin, $progressMax, 0);

        for ($i = $progressMin; $i <= $progressMax; $i++) {
            $progress->setCurrent($i);
            $progress->render();
            usleep(10000);
        }

    }

    /**
     * @return string
     */
    public function getCommandString(): string
    {
        return "orm:schema:update";
    }
}