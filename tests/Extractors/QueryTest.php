<?php

namespace Tests\Extractors;

use Tests\TestCase;

class QueryTest extends TestCase
{
    /** @test */
    public function extract_data_from_a_database_using_a_query_with_default_options()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->once())->method('execute')->with([]);
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls('row1', 'row2', null));

        $connection = $this->createMock('PDO');
        $connection->expects($this->once())->method('prepare')->with('select query')->willReturn($statement);

        $extractor = $this->getMockBuilder('Marquine\Etl\Extractors\Query')->setMethods(['db'])->getMock();
        $extractor->expects($this->once())->method('db')->with('default')->willReturn($connection);

        $extractor->source('select query');

        $this->assertEquals(['row1', 'row2'], iterator_to_array($extractor));
    }

    /** @test */
    public function extract_data_from_a_database_using_a_query_with_custom_options()
    {
        $statement = $this->createMock('PDOStatement');
        $statement->expects($this->once())->method('execute')->with('bindings');
        $statement->expects($this->exactly(3))->method('fetch')->will($this->onConsecutiveCalls('row1', 'row2', null));

        $connection = $this->createMock('PDO');
        $connection->expects($this->once())->method('prepare')->with('select query')->willReturn($statement);

        $extractor = $this->getMockBuilder('Marquine\Etl\Extractors\Query')->setMethods(['db'])->getMock();
        $extractor->expects($this->once())->method('db')->with('connection')->willReturn($connection);

        $extractor->connection = 'connection';
        $extractor->bindings = 'bindings';

        $extractor->source('select query');

        $this->assertEquals(['row1', 'row2'], iterator_to_array($extractor));
    }
}
