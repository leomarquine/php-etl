<?php

namespace Tests\Loaders;

use Tests\TestCase;
use Marquine\Etl\Loaders\Insert;

class InsertTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $this->pipeline->expects($this->any())->method('sample')->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);
        $this->pipeline->expects($this->any())->method('metadata')->willReturn('meta');

        $this->statement = $this->createMock('PDOStatement');
        $this->statement->expects($this->any())->method('execute');

        $this->transaction = $this->createMock('Marquine\Etl\Database\Transaction');
        $this->transaction->expects($this->any())->method('size')->willReturnSelf();
        $this->transaction->expects($this->any())->method('run')->with('meta', $this->isType('callable'))->willReturnCallback(function ($metadata, $callback) {
            call_user_func($callback);
        });

        $this->builder = $this->createMock('Marquine\Etl\Database\Statement');
        $this->builder->expects($this->any())->method('insert')->willReturnSelf();
        $this->builder->expects($this->any())->method('prepare')->willReturn($this->statement);

        $this->manager = $this->createMock('Marquine\Etl\Database\Manager');
        $this->manager->expects($this->any())->method('statement')->willReturn($this->builder);
        $this->manager->expects($this->any())->method('transaction')->willReturn($this->transaction);

        $this->loader = new Insert($this->manager);
        $this->loader->pipeline($this->pipeline);

        $this->data = ['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com'];
    }

    /** @test */
    public function loader_handler_must_return_the_row()
    {
        $handler = $this->loader->load('table');

        $this->assertEquals($this->data, call_user_func($handler, $this->data, 'meta'));
    }

    /** @test */
    public function default_options()
    {
        $this->manager->expects($this->once())->method('statement')->with('default')->willReturn($this->builder);
        $this->manager->expects($this->once())->method('transaction')->with('default')->willReturn($this->transaction);
        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email'])->willReturnSelf();
        $this->transaction->expects($this->once())->method('size')->with(100)->willReturnSelf();
        $this->transaction->expects($this->once())->method('run');

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function filtering_columns()
    {
        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe']);
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name'])->willReturnSelf();

        $this->loader->options(['columns' => ['id', 'name']]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function mapping_columns()
    {
        $this->statement->expects($this->once())->method('execute')->with(['user_id' => '1', 'full_name' => 'Jane Doe']);
        $this->builder->expects($this->once())->method('insert')->with('table', ['user_id', 'full_name'])->willReturnSelf();

        $this->loader->options(['columns' => [
            'id' => 'user_id',
            'name' => 'full_name',
        ]]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function without_transactions()
    {
        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email'])->willReturnSelf();
        $this->transaction->expects($this->never())->method('size');
        $this->transaction->expects($this->never())->method('run');
        $this->manager->expects($this->never())->method('transaction');

        $this->loader->options(['transaction' => false]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function with_timestamps()
    {
        $this->statement->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s')]);
        $this->builder->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email', 'created_at', 'updated_at'])->willReturnSelf();

        $this->loader->options(['timestamps' => true]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function custom_commit_size()
    {
        $this->transaction->expects($this->once())->method('size')->with(50)->willReturnSelf();

        $this->loader->options(['commit_size' => 50]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }
}
