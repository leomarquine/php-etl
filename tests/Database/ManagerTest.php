<?php

namespace Tests\Database;

use Tests\TestCase;
use Marquine\Etl\Database\Manager;

class ManagerTest extends TestCase
{
    /** @test */
    public function default_connection()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options']);

        $this->assertAttributeEquals(['default' => ['options']], 'config', $manager);
    }

    /** @test */
    public function connection_with_custom_name()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options'], 'custom');

        $this->assertAttributeEquals(['custom' => ['options']], 'config', $manager);
    }

    /** @test */
    public function get_a_query_builder_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        $this->assertInstanceOf('Marquine\Etl\Database\Query', $manager->query('default'));
    }

    /** @test */
    public function get_a_statement_builder_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        $this->assertInstanceOf('Marquine\Etl\Database\Statement', $manager->statement('default'));
    }

    /** @test */
    public function get_a_transaction_manager_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        $this->assertInstanceOf('Marquine\Etl\Database\Transaction', $manager->transaction('default'));
    }

    /** @test */
    public function get_the_pdo_connection()
    {
        $factory = $this->createMock('Marquine\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        $this->assertInstanceOf('PDO', $manager->pdo('default'));
    }

    /** @test */
    public function invalid_connection_throws_exception()
    {
        $manager = new Manager($this->createMock('Marquine\Etl\Database\ConnectionFactory'));

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Database [invalid] not configured.');

        $manager->pdo('invalid');
    }
}
