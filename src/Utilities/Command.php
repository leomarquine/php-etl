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
     * Command output handler.
     *
     * @var callable
     */
    public $handler;

    /**
     * Run the utility.
     *
     * @return void
     */
    public function run()
    {
        if (is_string($this->command)) {
            $output = $this->runCommand();
        }

        if (is_array($this->command)) {
            $output = $this->runCommands();
        }

        if (is_callable($this->handler)) {
            call_user_func($this->handler, $output);
        }
    }

    /**
     * Execute a single command.
     *
     * @return string
     */
    protected function runCommand()
    {
        return shell_exec($this->command);
    }

    /**
     * Execute multiple commands.
     *
     * @return array
     */
    protected function runCommands()
    {
        $output = [];

        foreach ($this->command as $command) {
            $output[] = shell_exec($command);
        }

        return $output;
    }
}
