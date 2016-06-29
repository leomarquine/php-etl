<?php

namespace Tests\Loaders;

use DateTime;
use Tests\TestCase;
use Marquine\Metis\Metis;

class TableTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    function insert_data_into_table()
    {
        Metis::extract('array', $this->users)
            ->load('table', 'users');

        $results = Metis::connection()->fetchAll('select * from users');

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function update_table_data()
    {
        $users = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'janedoe@email.com'],
        ];

        foreach ($users as $user) {
            Metis::connection()->insert('users', $user);
        }

        Metis::extract('array', $this->users)
            ->load('table', 'users');

        $results = Metis::connection()->fetchAll('select * from users');

        $this->assertEquals($this->users, $results);
    }

    /** @test */
    function delete_records_that_are_not_in_the_source()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users', $user);
        }

        $users = [
            ['id' => '1', 'name' => 'John', 'email' => 'johndoe@email.com'],
        ];

        Metis::extract('array', $users)
            ->load('table', 'users', ['delete' => true]);

        $results = Metis::connection()->fetchAll('select * from users');

        $this->assertEquals($users, $results);
    }

    /** @test */
    function insert_data_into_table_with_timestamps()
    {
        Metis::extract('array', $this->users)
            ->load('table', 'users_ts', ['timestamps' => true]);

        $results = Metis::connection()->fetchAll('select * from users_ts');

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['created_at']));
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['updated_at']));
            $this->assertEquals($row['created_at'], $row['updated_at']);
        }
    }

    /** @test */
    function update_table_data_with_timestamps()
    {
        $users = [
            ['id' => '1', 'name' => 'John', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane', 'email' => 'janedoe@email.com'],
        ];

        foreach ($users as $user) {
            Metis::connection()->insert('users_ts', $user);
        }

        Metis::extract('array', $this->users)
            ->load('table', 'users_ts', ['timestamps' => true]);

        $results = Metis::connection()->fetchAll('select * from users_ts');

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['updated_at']));
            $this->assertNull($row['created_at']);
        }
    }

    /** @test */
    function soft_delete_records_that_are_not_in_the_source()
    {
        foreach ($this->users as $user) {
            Metis::connection()->insert('users_ts', $user);
        }

        Metis::extract('array', [])
            ->load('table', 'users_ts', ['delete' => 'soft']);

        $results = Metis::connection()->fetchAll('select * from users_ts');

        $this->assertNotEmpty($results);

        foreach ($results as $row) {
            $this->assertTrue((bool) DateTime::createFromFormat('Y-m-d G:i:s', $row['deleted_at']));
        }
    }
}
