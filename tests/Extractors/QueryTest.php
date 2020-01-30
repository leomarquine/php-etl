<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\Query;
use Wizaplace\Etl\Row;

class QueryTest extends TestCase
{
    /** @test */
    public function default_options()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->once())->method('execute')->with([]);
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls(['row1'], ['row2'], null));

        $connection = $this->createMock('PDO');
        $connection->expects($this->once())->method('prepare')->with('select query')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects($this->once())->method('pdo')->with('default')->willReturn($connection);

        $extractor = new Query($manager);

        $extractor->input('select query');

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function custom_connection_and_bindings()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->once())->method('execute')->with('bindings');
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls(['row1'], ['row2'], null));

        $connection = $this->createMock('PDO');
        $connection->expects($this->once())->method('prepare')->with('select query')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects($this->once())->method('pdo')->with('connection')->willReturn($connection);

        $extractor = new Query($manager);

        $extractor->input('select query');
        $extractor->options([
            'connection' => 'connection',
            'bindings' => 'bindings',
        ]);

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }
}
