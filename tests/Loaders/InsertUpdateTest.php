<?php

namespace Tests\Loaders;

use Tests\TestCase;
use Marquine\Etl\Loaders\InsertUpdate;

class InsertUpdateTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->pipeline = $this->createMock('Marquine\Etl\Pipeline');
        $this->pipeline->expects($this->any())->method('sample')->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->transaction = $this->createMock('Marquine\Etl\Database\Transaction');
        $this->transaction->expects($this->any())->method('size')->willReturnSelf();
        $this->transaction->expects($this->any())->method('run')->with('meta', $this->isType('callable'))->willReturnCallback(function ($metadata, $callback) {
            call_user_func($callback);
        });

        $this->insert = $this->createMock('PDOStatement');
        $this->insert->expects($this->any())->method('execute');

        $this->insertStatement = $this->createMock('Marquine\Etl\Database\Statement');
        $this->insertStatement->expects($this->any())->method('prepare')->willReturn($this->insert);

        $this->select = $this->createMock('PDOStatement');
        $this->select->expects($this->any())->method('execute');

        $this->selectStatement = $this->createMock('Marquine\Etl\Database\Statement');
        $this->selectStatement->expects($this->any())->method('where')->willReturnSelf();
        $this->selectStatement->expects($this->any())->method('prepare')->willReturn($this->select);

        $this->update = $this->createMock('PDOStatement');
        $this->update->expects($this->any())->method('execute');

        $this->updateStatement = $this->createMock('Marquine\Etl\Database\Statement');
        $this->updateStatement->expects($this->any())->method('where')->willReturnSelf();
        $this->updateStatement->expects($this->any())->method('prepare')->willReturn($this->update);

        $this->statement = $this->createMock('Marquine\Etl\Database\Statement');
        $this->statement->expects($this->any())->method('insert')->willReturn($this->insertStatement);
        $this->statement->expects($this->any())->method('select')->willReturn($this->selectStatement);
        $this->statement->expects($this->any())->method('update')->willReturn($this->updateStatement);

        $this->manager = $this->createMock('Marquine\Etl\Database\Manager');
        $this->manager->expects($this->any())->method('statement')->willReturn($this->statement);
        $this->manager->expects($this->any())->method('transaction')->willReturn($this->transaction);

        $this->loader = new InsertUpdate($this->manager);
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
    public function insert_row_if_it_was_not_found_in_the_database()
    {
        $this->statement->expects($this->once())->method('select')->with('table');
        $this->selectStatement->expects($this->once())->method('where')->with(['id']);
        $this->selectStatement->expects($this->once())->method('prepare');
        $this->select->expects($this->once())->method('execute')->with(['id' => '1']);
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email']);
        $this->insertStatement->expects($this->once())->method('prepare');
        $this->insert->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->update->expects($this->never())->method('execute');

        $this->transaction->expects($this->once())->method('size')->with(100);
        $this->transaction->expects($this->once())->method('run');

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function update_row_if_it_was_found_in_the_database()
    {
        $this->statement->expects($this->once())->method('select')->with('table');
        $this->selectStatement->expects($this->once())->method('where')->with(['id']);
        $this->selectStatement->expects($this->once())->method('prepare');
        $this->select->expects($this->once())->method('execute')->with(['id' => '1']);
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['name', 'email']);
        $this->updateStatement->expects($this->once())->method('where')->with(['id']);
        $this->updateStatement->expects($this->once())->method('prepare');
        $this->update->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->insert->expects($this->never())->method('execute');

        $this->transaction->expects($this->once())->method('size')->with(100);
        $this->transaction->expects($this->once())->method('run');

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function do_not_update_if_there_are_no_changes()
    {
        $this->statement->expects($this->once())->method('select')->with('table');
        $this->selectStatement->expects($this->once())->method('where')->with(['id']);
        $this->selectStatement->expects($this->once())->method('prepare');
        $this->select->expects($this->once())->method('execute')->with(['id' => '1']);
        $this->select->expects($this->once())->method('fetch')->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s')]);

        $this->statement->expects($this->once())->method('update')->with('table', ['name', 'email']);
        $this->updateStatement->expects($this->once())->method('where')->with(['id']);
        $this->updateStatement->expects($this->once())->method('prepare');
        $this->update->expects($this->never())->method('execute');

        $this->insert->expects($this->never())->method('execute');

        $this->transaction->expects($this->once())->method('size')->with(100);
        $this->transaction->expects($this->once())->method('run');

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function filtering_columns_to_insert()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['id', 'name']);
        $this->insert->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id', 'name']]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function filtering_columns_to_update()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['name']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1',  'name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id', 'name']]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function mapping_columns_to_insert()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['user_id', 'full_name']);
        $this->insert->expects($this->once())->method('execute')->with(['user_id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->options(['columns' => [
            'id' => 'user_id',
            'name' => 'full_name',
        ]]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function mapping_columns_to_update()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['full_name']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->options(['columns' => [
            'id' => 'id',
            'name' => 'full_name',
        ]]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function insert_data_using_timestamps()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email', 'created_at', 'updated_at']);
        $this->insert->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s')]);

        $this->loader->options(['timestamps' => true]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function update_data_using_timestamps()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['name', 'email', 'updated_at']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'updated_at' => date('Y-m-d G:i:s')]);

        $this->loader->options(['timestamps' => true]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function insert_data_into_the_database_without_transactions()
    {
        $this->transaction->expects($this->never())->method('size');
        $this->transaction->expects($this->never())->method('run');
        $this->manager->expects($this->never())->method('transaction');

        $this->select->expects($this->once())->method('fetch')->willReturn(false);
        $this->insert->expects($this->once())->method('execute');

        $this->loader->options(['transaction' => false]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }

    /** @test */
    public function update_data_into_the_database_without_transactions()
    {
        $this->transaction->expects($this->never())->method('size');
        $this->transaction->expects($this->never())->method('run');
        $this->manager->expects($this->never())->method('transaction');

        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);
        $this->update->expects($this->once())->method('execute');

        $this->loader->options(['transaction' => false]);

        $handler = $this->loader->load('table');

        call_user_func($handler, $this->data, 'meta');
    }
}
