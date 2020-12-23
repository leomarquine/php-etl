<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Database;

use Tests\TestCase;
use Wizaplace\Etl\Database\Manager;

class ManagerTest extends TestCase
{
    /** @test */
    public function defaultConnection(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options']);

        static::assertEquals(['default' => ['options']], $manager->getConfig());
    }

    /** @test */
    public function connectionWithCustomName(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');

        $manager = new Manager($factory);
        $manager->addConnection(['options'], 'custom');

        static::assertEquals(['custom' => ['options']], $manager->getConfig());
    }

    /** @test */
    public function getQueryBuilderInstanceForGivenConnection(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects(static::once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Query', $manager->query('default'));
    }

    /** @test */
    public function getStatementBuilderInstanceForGivenConnection(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects(static::once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Statement', $manager->statement('default'));
    }

    /** @test */
    public function getTransactionManagerInstanceForGivenConnection(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects(static::once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('Wizaplace\Etl\Database\Transaction', $manager->transaction('default'));
    }

    /** @test */
    public function getPdoConnection(): void
    {
        $factory = $this->createMock('Wizaplace\Etl\Database\ConnectionFactory');
        $factory->expects(static::once())->method('make')->with([])->willReturn($this->createMock('PDO'));

        $manager = new Manager($factory);
        $manager->addConnection([]);

        static::assertInstanceOf('PDO', $manager->pdo('default'));
    }

    /** @test */
    public function invalidConnectionThrowsException(): void
    {
        $manager = new Manager($this->createMock('Wizaplace\Etl\Database\ConnectionFactory'));

        $this->expectException('InvalidArgumentException');
        $this->expectExceptionMessage('Database [invalid] not configured.');

        $manager->pdo('invalid');
    }
}
