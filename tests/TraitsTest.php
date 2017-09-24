<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Traits\ValidateSource;

class TraitsTest extends TestCase
{
    use ValidateSource;

    /** @test */
    function validate_source()
    {
        $this->assertTrue(is_file($this->validateSource('users.csv')));

        $this->assertTrue(is_file($this->validateSource(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'users.csv')));

        $this->assertFalse(is_file($this->validateSource('notfound.csv')));

        $this->assertEquals('http://www.leomarquine.com', $this->validateSource('http://www.leomarquine.com'));
    }
}
