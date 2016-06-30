<?php

namespace Marquine\Metis\Utilities;

use Marquine\Metis\Contracts\Utility;
use Marquine\Metis\Traits\SetOptions;

class Command implements Utility
{
    use SetOptions;

    /**
    * List of commands to run.
    *
    * @var array
    */
    protected $command;

    /**
    * Handle the utility.
    *
    * @return void
    */
    public function handle()
    {
        if (is_string($this->command)) {
            $this->command = [$this->command];
        }

        foreach ($this->command as $command) {
            shell_exec($command);
        }
    }
}
