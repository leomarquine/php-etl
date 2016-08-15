<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Traits\Indexable;
use Marquine\Etl\Traits\ValidateSource;

class TraitsTest extends TestCase
{
    use Indexable, ValidateSource;

    /** @test */
    function indexable()
    {
        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $users = $this->index($items, ['id', 'name']);

        $expected = [
            '1John Doe' => ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            '2Jane Doe' => ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, $users);
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
