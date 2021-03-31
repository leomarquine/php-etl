<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Loaders;

use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;
use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Database\Statement;
use Wizaplace\Etl\Database\Transaction;
use Wizaplace\Etl\Loaders\Insert;
use Wizaplace\Etl\Row;

class InsertTest extends TestCase
{
    /** @var \PDOStatement|MockObject */
    private $statement;

    /** @var MockObject|Transaction */
    private $transaction;

    /** @var MockObject|Statement */
    private $builder;

    /** @var MockObject|Manager */
    private $manager;

    /** @var MockObject|Row */
    private $row;

    private Insert $loader;

    protected function setUp(): void
    {
        parent::setUp();

        $this->statement = $this->createMock('PDOStatement');
        $this->statement->expects(static::any())->method('execute');

        $this->transaction = $this->createMock('Wizaplace\Etl\Database\Transaction');
        $this->transaction->expects(static::any())->method('size')->willReturnSelf();
        $this->transaction->expects(static::any())->method('run')->willReturnCallback(function ($callback): void {
            $callback();
        });
        $this->transaction->expects(static::any())->method('close');

        $this->builder = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->builder->expects(static::any())->method('insert')->willReturnSelf();
        $this->builder->expects(static::any())->method('prepare')->willReturn($this->statement);

        $this->manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $this->manager->expects(static::any())->method('statement')->willReturn($this->builder);
        $this->manager->expects(static::any())->method('transaction')->willReturn($this->transaction);

        $this->row = $this->createMock('Wizaplace\Etl\Row');
        $this->row->expects(static::any())->method('toArray')
            ->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader = new Insert($this->manager);
    }

    /** @test */
    public function insert(): void
    {
        $this->manager->expects(static::once())->method('statement')->with('default');
        $this->manager->expects(static::once())->method('transaction')->with('default');

        $this->transaction->expects(static::once())->method('size')->with(0);
        $this->transaction->expects(static::once())->method('run');
        $this->transaction->expects(static::once())->method('close');

        $this->builder->expects(static::once())->method('insert')->with('table', ['id', 'name', 'email']);
        $this->builder->expects(static::once())->method('prepare');

        $this->statement->expects(static::once())->method('execute')
            ->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader->output('table');

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function filteringColumns(): void
    {
        $this->builder->expects(static::once())->method('insert')->with('table', ['id', 'name']);

        $this->statement->expects(static::once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe']);

        $this->loader->output('table');
        $this->loader->options([$this->loader::COLUMNS => ['id', 'name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function mappingColumns(): void
    {
        $this->builder->expects(static::once())->method('insert')->with('table', ['user_id', 'full_name']);

        $this->statement->expects(static::once())->method('execute')
            ->with(['user_id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->output('table');
        $this->loader->options([$this->loader::COLUMNS => ['id' => 'user_id', 'name' => 'full_name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function withoutTransactions(): void
    {
        $this->manager->expects(static::never())->method('transaction');

        $this->transaction->expects(static::never())->method('size');
        $this->transaction->expects(static::never())->method('run');

        $this->builder->expects(static::once())->method('insert')->with('table', ['id', 'name', 'email']);

        $this->statement->expects(static::once())->method('execute')
            ->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader->output('table');
        $this->loader->options([$this->loader::TRANSACTION => false]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function withTimestamps(): void
    {
        $this->builder->expects(static::once())->method('insert')
            ->with('table', ['id', 'name', 'email', 'created_at', 'updated_at']);

        $this->statement->expects(static::once())->method('execute')->with([
            'id' => '1',
            'name' => 'Jane Doe',
            'email' => 'janedoe@example.com',
            'created_at' => date('Y-m-d G:i:s'),
            'updated_at' => date('Y-m-d G:i:s'),
        ]);

        $this->loader->output('table');
        $this->loader->options([$this->loader::TIMESTAMPS => true]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function customCommitSize(): void
    {
        $this->transaction->expects(static::once())->method('size')->with(50)->willReturnSelf();

        $this->loader->output('table');
        $this->loader->options([$this->loader::COMMIT_SIZE => 50]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function customConnection(): void
    {
        $this->manager->expects(static::once())->method('statement')->with('custom');
        $this->manager->expects(static::once())->method('transaction')->with('custom');

        $this->loader->output('table');
        $this->loader->options([$this->loader::CONNECTION => 'custom']);

        $this->execute($this->loader, [$this->row]);
    }
}
