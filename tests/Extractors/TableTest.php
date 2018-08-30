<?php

namespace Tests\Extractors;

use Tests\TestCase;
use Marquine\Etl\Extractors\Table;

class TableTest extends TestCase
{
    /** @test */
    public function extract_from_a_database_table_with_default_options()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls('row1', 'row2', null));

        $query = $this->createMock('Marquine\Etl\Database\Query');
        $query->expects($this->once())->method('select')->with('table', ['*'])->will($this->returnSelf());
        $query->expects($this->once())->method('where')->with([])->will($this->returnSelf());
        $query->expects($this->once())->method('execute')->willReturn($statement);

        $manager = $this->createMock('Marquine\Etl\Database\Manager');
        $manager->expects($this->once())->method('query')->with('default')->willReturn($query);

        $extractor = new Table($manager);
        $extractor->source('table');

        $this->assertEquals(['row1', 'row2'], iterator_to_array($extractor));
    }

    /** @test */
    public function extract_from_a_database_table_with_custom_options()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls('row1', 'row2', null));

        $query = $this->createMock('Marquine\Etl\Database\Query');
        $query->expects($this->once())->method('select')->with('table', 'columns')->will($this->returnSelf());
        $query->expects($this->once())->method('where')->with('where')->will($this->returnSelf());
        $query->expects($this->once())->method('execute')->willReturn($statement);

        $manager = $this->createMock('Marquine\Etl\Database\Manager');
        $manager->expects($this->once())->method('query')->with('connection')->willReturn($query);

        $extractor = new Table($manager);
        $extractor->connection = 'connection';
        $extractor->columns = 'columns';
        $extractor->where = 'where';

        $extractor->source('table');

        $this->assertEquals(['row1', 'row2'], iterator_to_array($extractor));
    }
}
