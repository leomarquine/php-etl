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
use Wizaplace\Etl;
use Wizaplace\Etl\Database\ConnectionFactory;
use Wizaplace\Etl\Database\Manager;
use Wizaplace\Etl\Extractors\Table;
use Wizaplace\Etl\Row;

class TableTest extends TestCase
{
    /** @test */
    public function defaultOptions(): void
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects(static::exactly(3))->method('fetch')
            ->will(static::onConsecutiveCalls(['row1'], ['row2'], null));

        $query = $this->createMock('Wizaplace\Etl\Database\Query');
        $query->expects(static::once())->method('select')->with('table', ['*'])->will(static::returnSelf());
        $query->expects(static::once())->method('where')->with([])->will(static::returnSelf());
        $query->expects(static::once())->method('execute')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects(static::once())->method('query')->with('default')->willReturn($query);

        $extractor = new Table($manager);

        $extractor->input('table');

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }

    /** @test */
    public function customConnectionColumnsAndWhereClause(): void
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects(static::exactly(3))->method('fetch')
            ->will(static::onConsecutiveCalls(['row1'], ['row2'], null));

        $query = $this->createMock('Wizaplace\Etl\Database\Query');
        $query->expects(static::once())->method('select')->with('table', ['columns'])->will(static::returnSelf());
        $query->expects(static::once())->method('where')->with(['where'])->will(static::returnSelf());
        $query->expects(static::once())->method('execute')->willReturn($statement);

        $manager = $this->createMock('Wizaplace\Etl\Database\Manager');
        $manager->expects(static::once())->method('query')->with('connection')->willReturn($query);

        $extractor = new Table($manager);

        $extractor->input('table');
        $extractor->options([
            'connection' => 'connection',
            'columns' => ['columns'],
            'where' => ['where'],
        ]);

        static::assertEquals([new Row(['row1']), new Row(['row2'])], iterator_to_array($extractor->extract()));
    }

    /**
     * Tests extended where-clause comparisons (e.g., <>, <, >, <=, >=).
     *
     * @param array           $expected the expected result of table extraction
     * @param string|string[] $where    the where clause used in filtering
     *
     * @test
     * @dataProvider whereClauseDataProvider
     */
    public function whereClauseOperators(array $expected, $where): void
    {
        // Set up connection to SQLite test database.
        $connection = 'default';
        $name = tempnam(sys_get_temp_dir(), 'etl');
        $config = ['driver' => 'sqlite', 'database' => $name];
        $manager = new Manager(new ConnectionFactory());
        $manager->addConnection($config, $connection);

        // Instantiate a table for testing.
        $database = $manager->pdo($connection);
        $table = 'unit';
        $column = 'column';
        $database->exec("CREATE TABLE $table ($column VARCHAR(20))");
        $database->exec("INSERT INTO $table VALUES ('row1')");
        $database->exec("INSERT INTO $table VALUES ('row2')");

        // Perform the test using data provider arrays for where condition and expected result.
        $pipeline = new Etl\Etl();
        $options = [
            'connection' => 'default',
            'columns' => [$column],
            'where' => [$column => $where],
        ];
        $actual = $pipeline->extract(new Table($manager), $table, $options)->toArray();
        self::assertEquals($expected, $actual);

        // Clean up our temporary database.
        unlink($name);
    }

    /**
     * Provides test case scenarios for {@see whereClauseOperators()}.
     */
    public function whereClauseDataProvider(): array
    {
        return [
            [[], ['<', 'row1']],
            [[['column' => 'row1']], 'row1'],
            [[['column' => 'row1']], ['=', 'row1']],
            [[['column' => 'row2']], ['>', 'row1']],
            [[['column' => 'row2']], ['>=', 'row2']],
            [[['column' => 'row1']], ['<>', 'row2']],
            [[['column' => 'row1'], ['column' => 'row2']], ['<=', 'row2']],
            [[['column' => 'row1'], ['column' => 'row2']], ['<>', 'row3']],
        ];
    }
}
