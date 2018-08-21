<?php

namespace Tests\Database;

use Tests\TestCase;
use Marquine\Etl\Database\Connection;

class ConnectionTest extends TestCase
{
    /** @test */
    public function dynamically_pass_method_calls_to_the_pdo_instance()
    {
        $pdo = $this->createMock('PDO');
        $pdo->expects($this->once())->method('exec')->with('statement');

        $connection = new Connection($pdo);

        $connection->exec('statement');
    }
}
