<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @author      Karl DeBisschop <kdebisschop@gmail.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Transformers;

use Tests\TestCase;
use Wizaplace\Etl\Row;
use Wizaplace\Etl\Transformers\Callback;

class CallbackTest extends TestCase
{
    /**
     * Row array to be transformed in testing.
     *
     * @var Row[]
     */
    protected $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'name' => 'John Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Doe', 'email' => 'janeDoe@email.com']),
        ];
    }

    public function transformOneColumn(): void
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John', 'Doe', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane', 'Doe', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane', 'Doe', 'email' => 'janeDoe@email.com']),
        ];

        $callback = function (Row $row, $column) {
            return explode(' ', $row->get($column));
        };

        $transformer = new Callback();

        $transformer->options(['columns' => ['name'], 'callback' => $callback]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function useDataFromOtherColumn()
    {
        $expected = [
            new Row(['id' => '1', 'name' => 'John Doe <johndoe@email.com>', 'email' => 'johndoe@email.com']),
            new Row(['id' => '2', 'name' => 'Jane Doe <janedoe@email.com>', 'email' => 'janedoe@email.com']),
            new Row(['id' => '3', 'name' => 'Jane Doe <janeDoe@email.com>', 'email' => 'janeDoe@email.com']),
        ];

        $callback = function (Row $row, $column) {
            return "{$row->get($column)} <{$row->get('email')}>";
        };

        $transformer = new Callback();

        $transformer->options(['columns' => ['name'], 'callback' => $callback]);

        $this->execute($transformer, $this->data);

        static::assertEquals($expected, $this->data);
    }
}
