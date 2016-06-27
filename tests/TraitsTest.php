<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Metis\Traits\Indexable;
use Marquine\Metis\Traits\SetOptions;
use Marquine\Metis\Traits\ValidateSource;

class TraitsTest extends TestCase
{
    use Indexable, ValidateSource;

    /** @test */
    function indexable()
    {
        $users = $this->index($this->users, ['id', 'name']);

        $expected = [
            '1John Doe' => ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            '2Jane Doe' => ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, $users);
    }

    /** @test */
    function set_options()
    {
        $class = new Options(['property' => true]);

        $this->assertTrue($class->property);
    }

    /** @test */
    function validate_source()
    {
        $this->assertTrue(is_file($this->validateSource('users.csv')));

        $this->assertTrue(is_file($this->validateSource(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'users.csv')));

        $this->assertFalse(is_file($this->validateSource('notfound.csv')));

        $this->assertEquals('http://www.leomarquine.com', $this->validateSource('http://www.leomarquine.com'));
    }
}

class Options
{
    use SetOptions;

    public $property = false;
}
