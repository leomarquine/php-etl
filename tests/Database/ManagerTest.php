<?php

namespace Tests\Database;

use Mockery;
use Tests\TestCase;
use Marquine\Etl\Etl;
use Marquine\Etl\Database\Manager as DB;
use Marquine\Etl\Database\ConnectionFactory;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ManagerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function create_a_new_connection()
    {
        Etl::addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ], 'test');

        $factory = Mockery::mock(ConnectionFactory::class);
        $factory->shouldReceive('make')->with(['driver' => 'sqlite', 'database' => ':memory:'])->andReturn('connection');

        $this->assertEquals('connection', DB::connection('test', $factory));
    }
}
