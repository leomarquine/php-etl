<?php

namespace Tests;

use Tests\TestCase;
use Marquine\Etl\Support\ValidateSource;

class ValidateSourceTeste extends TestCase
{
    use ValidateSource;

    /** @test */
    function validate_source()
    {
        $this->assertTrue(is_file($this->validateSource('csv1.csv')));

        $this->assertTrue(is_file($this->validateSource(__DIR__ . '/../data/csv1.csv')));

        $this->assertFalse(is_file($this->validateSource('notfound.csv')));

        $this->assertEquals('http://www.leomarquine.com', $this->validateSource('http://www.leomarquine.com'));
    }
}
