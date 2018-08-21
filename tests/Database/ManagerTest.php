<?php

namespace Tests\Database;

use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Database\Manager as DB;

class ManagerTest extends TestCase
{
    /** @test */
    public function create_a_new_connection()
    {
        Etl::addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ], 'test');

        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with(['driver' => 'sqlite', 'database' => ':memory:'])->willReturn('connection');

        $this->assertEquals('connection', DB::connection('test', $factory));
    }
}
