<?php

namespace Marquine\Etl\Utilities;

class Command implements UtilityInterface
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
    * Handle the utility.
    *
    * @return void
    */
    public function handle()
    {
        if ($this->command) {
            shell_exec($this->command);
        }

        foreach ($this->commands as $command) {
            shell_exec($command);
        }
    }
}
