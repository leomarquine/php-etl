<?php

namespace Tests\Utilities;

use Tests\TestCase;
use FilesystemIterator;
use Marquine\Etl\Utilities\Command;

class CommandTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        mkdir($this->path());
    }

    protected function tearDown()
    {
        parent::tearDown();

        $items = new FilesystemIterator($this->path());

        foreach ($items as $item) {
            unlink($item->getPathname());
        }

        rmdir($this->path());
    }

    protected function path($file = null)
    {
        return __DIR__.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR.($file ?: '');
    }

    /** @test */
    public function execute_a_command()
    {
        $utility = new Command;

        $utility->command = "touch {$this->path('file.txt')}";

        $utility->run();

        $this->assertFileExists($this->path('file.txt'));
    }

    /** @test */
    public function execute_multiple_commands()
    {
        $utility = new Command;

        $utility->command = [
            "touch {$this->path('file.txt')}",
            "mv {$this->path('file.txt')} {$this->path('new.txt')}",
        ];

        $utility->run();

        $this->assertFileExists($this->path('new.txt'));
    }

    /** @test */
    public function handle_output_of_a_single_command()
    {
        $utility = new Command;

        $result;

        $utility->command = 'echo foobar';
        $utility->handler = function ($output) use (&$result) {
            $result = $output;
        };

        $utility->run();

        $this->assertEquals('foobar', trim($result));
    }

    /** @test */
    public function handle_output_of_multiple_commands()
    {
        $utility = new Command;

        $result;

        $utility->command = ['echo foo', 'echo bar'];
        $utility->handler = function ($output) use (&$result) {
            $result = $output;
        };

        $utility->run();

        $this->assertEquals(['foo', 'bar'], array_map('trim', $result));
    }
}
