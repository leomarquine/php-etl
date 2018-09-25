<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Transformers\RenameColumns;

class RenameColumnsTest extends TestCase
{
    /** @test */
    public function rename_column()
    {
        $items = [
            ['id' => '1', 'name' => 'John Doe', 'email_address' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email_address' => 'janedoe@email.com'],
        ];

        $pipeline = $this->createMock('Marquine\Etl\Pipeline');

        $transformer = new RenameColumns;

        $transformer->columns = [
            'email_address' => 'email',
        ];

        $results = array_map($transformer->handler($pipeline), $items);

        $expected = [
            ['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com'],
            ['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com'],
        ];

        $this->assertEquals($expected, $results);
    }
}
