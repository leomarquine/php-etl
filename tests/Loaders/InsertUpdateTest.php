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
use Wizaplace\Etl\Loaders\InsertUpdate;

class InsertUpdateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->transaction = $this->createMock('Wizaplace\Etl\Database\Transaction');
        $this->transaction->expects($this->any())->method('size')->willReturnSelf();
        $this->transaction->expects($this->any())->method('run')->willReturnCallback(function ($callback) { call_user_func($callback); });
        $this->transaction->expects($this->any())->method('close');

        $this->insert = $this->createMock('PDOStatement');
        $this->insert->expects($this->any())->method('execute');

        $this->insertStatement = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->insertStatement->expects($this->any())->method('insert')->willReturnSelf();
        $this->insertStatement->expects($this->any())->method('prepare')->willReturn($this->insert);

        $this->select = $this->createMock('PDOStatement');
        $this->select->expects($this->any())->method('execute');

        $this->selectStatement = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->selectStatement->expects($this->any())->method('select')->willReturnSelf();
        $this->selectStatement->expects($this->any())->method('where')->willReturnSelf();
        $this->selectStatement->expects($this->any())->method('prepare')->willReturn($this->select);

        $this->update = $this->createMock('PDOStatement');
        $this->update->expects($this->any())->method('execute');

        $this->updateStatement = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->updateStatement->expects($this->any())->method('update')->willReturnSelf();
        $this->updateStatement->expects($this->any())->method('where')->willReturnSelf();
        $this->updateStatement->expects($this->any())->method('prepare')->willReturn($this->update);

        $this->statement = $this->createMock('Wizaplace\Etl\Database\Statement');
        $this->statement->expects($this->any())->method('insert')->willReturn($this->insertStatement);
        $this->statement->expects($this->any())->method('select')->willReturn($this->selectStatement);
        $this->statement->expects($this->any())->method('update')->willReturn($this->updateStatement);

        $this->manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $this->manager->expects($this->any())->method('statement')->willReturn($this->statement);
        $this->manager->expects($this->any())->method('transaction')->willReturn($this->transaction);

        $this->row = $this->createMock('Wizaplace\Etl\Row');
        $this->row->expects($this->any())->method('toArray')->willReturn(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->loader = new InsertUpdate($this->manager);
        $this->loader->output('table');
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
        $this->transaction->expects($this->once())->method('close');

        $this->execute($this->loader, [$this->row]);
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
        $this->transaction->expects($this->once())->method('close');

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function insert_row_even_if_updates_are_suppressed()
    {
        $this->loader->options(['doUpdates' => false]);
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
        $this->transaction->expects($this->once())->method('close');

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function do_not_update_or_insert_row_if_updates_are_suppressed()
    {
        $this->loader->options(['doUpdates' => false]);
        $this->statement->expects($this->once())->method('select')->with('table');
        $this->selectStatement->expects($this->once())->method('where')->with(['id']);
        $this->selectStatement->expects($this->once())->method('prepare');
        $this->select->expects($this->once())->method('execute')->with(['id' => '1']);
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->never())->method('update')->with('table', ['name', 'email']);
        $this->statement->expects($this->never())->method('insert')->with('table', ['id', 'name', 'email']);
        $this->updateStatement->expects($this->never())->method('where')->with(['id']);
        $this->updateStatement->expects($this->never())->method('prepare');
        $this->update->expects($this->never())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com']);

        $this->insert->expects($this->never())->method('execute');

        $this->transaction->expects($this->once())->method('size')->with(100);
        $this->transaction->expects($this->once())->method('run');
        $this->transaction->expects($this->once())->method('close');

        $this->execute($this->loader, [$this->row]);
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

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function filtering_columns_to_insert()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['id', 'name']);
        $this->insert->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id', 'name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function filtering_columns_to_update()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['name']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1',  'name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id', 'name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function mapping_columns_to_insert()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['user_id', 'full_name']);
        $this->insert->expects($this->once())->method('execute')->with(['user_id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id' => 'user_id', 'name' => 'full_name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function mapping_columns_to_update()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['full_name']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1', 'full_name' => 'Jane Doe']);

        $this->loader->options(['columns' => ['id' => 'id', 'name' => 'full_name']]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function insert_data_using_timestamps()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(false);

        $this->statement->expects($this->once())->method('insert')->with('table', ['id', 'name', 'email', 'created_at', 'updated_at']);
        $this->insert->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'created_at' => date('Y-m-d G:i:s'), 'updated_at' => date('Y-m-d G:i:s')]);

        $this->loader->options(['timestamps' => true]);

        $this->execute($this->loader, [$this->row]);
    }

    /** @test */
    public function update_data_using_timestamps()
    {
        $this->select->expects($this->once())->method('fetch')->willReturn(['name' => 'Jane']);

        $this->statement->expects($this->once())->method('update')->with('table', ['name', 'email', 'updated_at']);
        $this->update->expects($this->once())->method('execute')->with(['id' => '1', 'name' => 'Jane Doe', 'email' => 'janedoe@example.com', 'updated_at' => date('Y-m-d G:i:s')]);

        $this->loader->options(['timestamps' => true]);

        $this->execute($this->loader, [$this->row]);
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

        $this->execute($this->loader, [$this->row]);
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

        $this->execute($this->loader, [$this->row]);
    }
}
