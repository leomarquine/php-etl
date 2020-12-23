<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Extractors;

use Tests\TestCase;
use Wizaplace\Etl\Extractors\Query;
use Wizaplace\Etl\Row;

class QueryTest extends TestCase
{
    /** @test */
    public function defaultOptions(): void
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects(static::once())->method('execute')->with([]);
        $statement->expects(static::exactly(3))->method('fetch')
            ->will(static::onConsecutiveCalls(['row1'], ['row2'], null));

        $connection = $this->createMock('PDO');
        $connection->expects(static::once())->method('prepare')->with('select query')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects(static::once())->method('pdo')->with('default')->willReturn($connection);

        $extractor = new Query($manager);

        $extractor->input('select query');

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customConnectionAndBindings(): void
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects(static::once())->method('execute')->with(['binding']);
        $statement->expects(static::exactly(3))->method('fetch')
            ->will(static::onConsecutiveCalls(['row1'], ['row2'], null));

        $connection = $this->createMock('PDO');
        $connection->expects(static::once())->method('prepare')->with('select query')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects(static::once())->method('pdo')->with('connection')->willReturn($connection);

        $extractor = new Query($manager);

        $extractor->input('select query');
        $extractor->options([
            'connection' => 'connection',
            'bindings' => ['binding'],
        ]);

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }
}
