<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Loaders;

use Tests\TestCase;
use Wizaplace\Etl\Loaders\Insert;

class InsertTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->statement = $this->createMock('PDOStatement');
        $this->statement->expects($this->any())->method('execute');

        $this->transaction = $this->createMock('Wizaplace\Etl\Database\Transaction');
        $this->transaction->expects($this->any())->method('size')->willReturnSelf();
        $this->transaction->expects($this->any())->method('run')->willReturnCallback(function ($callback) { call_user_func($callback); });
        $this->transaction->expects($this->any())->method('close');

        $this->builder = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->builder->expects($this->any())->method('insert')->willReturnSelf();
        $this->builder->expects($this->any())->method('prepare')->willReturn($this->statement);

        $this->manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $this->manager->expects($this->any())->method('statement')->willReturn($this->builder);
        $this->manager->expects($this->any())->method('transaction')->willReturn($this->transaction);

        $this->row = $this->createMock('Wizaplace\Etl\Row');
        $this->row->expects($this->any())->method('toArray')->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader = new Insert($this->manager);
    }

    /** @test */
    public function insert()
    {
        $this->manager->expects($this->once())->method('statement')->with('default');
        $this->manager->expects($this->once())->method('transaction')->with('default');

        $this->transaction->expects($this->once())->method('size')->with(100);
        $this->transaction->expects($this->once())->method('run');
        $this->transaction->expects($this->once())->method('close');

        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email']);
        $this->builder->expects($this->once())->method('prepare');

        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader->output('table');

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function filtering_columns()
    {
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name']);

        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe']);

        $this->loader->output('table');
        $this->loader->options(['columns' => ['id', 'name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function mapping_columns()
    {
        $this->builder->expects($this->once())->method('insert')->with('table', ['user_id', 'full_name']);

        $this->statement->expects($this->once())->method('execute')->with(['user_id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->output('table');
        $this->loader->options(['columns' => ['id' => 'user_id', 'name' => 'full_name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function without_transactions()
    {
        $this->manager->expects($this->never())->method('transaction');

        $this->transaction->expects($this->never())->method('size');
        $this->transaction->expects($this->never())->method('run');

        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email']);

        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader->output('table');
        $this->loader->options(['transaction' => false]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function with_timestamps()
    {
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email', 'created_at', 'updated_at']);

        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s')]);

        $this->loader->output('table');
        $this->loader->options(['timestamps' => true]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function custom_commit_size()
    {
        $this->transaction->expects($this->once())->method('size')->with(50)->willReturnSelf();

        $this->loader->output('table');
        $this->loader->options(['commit_size' => 50]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function custom_connection()
    {
        $this->manager->expects($this->once())->method('statement')->with('custom');
        $this->manager->expects($this->once())->method('transaction')->with('custom');

        $this->loader->output('table');
        $this->loader->options(['connection' => 'custom']);

        $this->execute($this->loader, [$this->row]);
    }
}
