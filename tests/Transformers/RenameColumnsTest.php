<?php

namespace Tests\Transformers;

use Tests\TestCase;
use Marquine\Etl\Row;
use Marquine\Etl\Transformers\RenameColumns;

class RenameColumnsTest extends TestCase
{
    /** @test */
    public function rename_column()
    {
        $data = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email_address' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email_address' => 'janedoe@email.com']),
        ];

        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
        ];

        $transformer = new RenameColumns;

        $transformer->options(['columns' => ['email_address' => 'email']]);

        $this->execute($transformer, $data);

        $this->assertEquals($expected, $data);
    }
}
