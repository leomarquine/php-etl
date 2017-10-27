<?php

namespace Tests\Database;

use PDO;
use Mockery;
use Tests\TestCase;
use Marquine\Etl\Database\Connection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;

class ConnectionTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /** @test */
    public function dynamically_pass_method_calls_to_the_pdo_instance()
    {
        $pdo = Mockery::mock(PDO::class);

        $connection = new Connection($pdo);

        $pdo->shouldReceive('method')->once()->with('param');

        $connection->method('param');
    }
}
