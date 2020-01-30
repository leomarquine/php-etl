<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Database;

use Tests\TestCase;
use Wizaplace\Etl\Database\Manager;

class ManagerTest extends TestCase
{
    /** @test */
    public function default_connection()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options']);

        static::assertEquals(['default' => ['options']], $manager->getConfig());
    }

    /** @test */
    public function connection_with_custom_name()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options'], 'custom');

        static::assertEquals(['custom' => ['options']], $manager->getConfig());
    }

    /** @test */
    public function get_a_query_builder_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Query', $manager->query('default'));
    }

    /** @test */
    public function get_a_statement_builder_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Statement', $manager->statement('default'));
    }

    /** @test */
    public function get_a_transaction_manager_instance_for_the_given_connection()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Transaction', $manager->transaction('default'));
    }

    /** @test */
    public function get_the_pdo_connection()
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects($this->once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('PDO', $manager->pdo('default'));
    }

    /** @test */
    public function invalid_connection_throws_exception()
    {
        $manager = new Manager($this->createMock('Wizaplace\Etl\Database\ConnectionFactory'));

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Database [invalid] not configured.');

        $manager->pdo('invalid');
    }
}
