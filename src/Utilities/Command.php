<?php

namespace Marquine\Etl\Utilities;

class Command extends Utility
{
    /**
     * Command to run.
     *
     * @var array
     */
    public $command;

    /**
     * List of commands to run.
     *
     * @var array
     */
    public $commands = [];

    /**
     * Run the utility.
     *
     * @return void
     */
    public function run()
    {
        if ($this->command) {
            shell_exec($this->command);
        }

        foreach ($this->commands as $command) {
            shell_exec($command);
        }
    }
}
