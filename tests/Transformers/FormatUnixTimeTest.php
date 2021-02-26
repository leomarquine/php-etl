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
use Wizaplace\Etl\Transformers\FormatUnixTime;

class FormatUnixTimeTest extends TestCase
{
    /**
     * Row array to be transformed in testing.
     *
     * @var Row[]
     */
    protected array $data;

    protected function setUp(): void
    {
        parent::setUp();

        $this->data = [
            new Row(['id' => '1', 'timestamp' => '1', 'unixtime' => '1']),
            new Row(['id' => '2', 'timestamp' => '2', 'unixtime' => '2']),
            new Row(['id' => '3', 'timestamp' => '1606613796', 'unixtime' => '1606613796']),
        ];
    }

    /** @test */
    public function setUtc(): void
    {
        $expected = [
            new Row(['id' => '1', 'timestamp' => '19700101', 'unixtime' => '1']),
            new Row(['id' => '2', 'timestamp' => '19700101', 'unixtime' => '2']),
            new Row(['id' => '3', 'timestamp' => '20201129', 'unixtime' => '1606613796']),
        ];
        $transformer = new FormatUnixTime();
        $transformer->options(['columns' => ['timestamp'], 'timezone' => 'UTC']);
        $this->execute($transformer, $this->data);
        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function setAmericaNewYork(): void
    {
        $expected = [
            new Row(['id' => '1', 'timestamp' => '19691231', 'unixtime' => '1']),
            new Row(['id' => '2', 'timestamp' => '19691231', 'unixtime' => '2']),
            new Row(['id' => '3', 'timestamp' => '20201128', 'unixtime' => '1606613796']),
        ];
        $transformer = new FormatUnixTime();
        $transformer->options(['columns' => ['timestamp'], 'timezone' => 'America/New_York']);
        $this->execute($transformer, $this->data);
        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function changeFormat(): void
    {
        $expected = [
            new Row(['id' => '1', 'timestamp' => '1970-01-01 00:00:01', 'unixtime' => '1']),
            new Row(['id' => '2', 'timestamp' => '1970-01-01 00:00:02', 'unixtime' => '2']),
            new Row(['id' => '3', 'timestamp' => '2020-11-29 01:36:36', 'unixtime' => '1606613796']),
        ];
        $transformer = new FormatUnixTime();
        $transformer->options(['columns' => ['timestamp'], 'timezone' => 'UTC', 'format' => 'Y-m-d H:i:s']);
        $this->execute($transformer, $this->data);
        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function selectMultipleColumns(): void
    {
        $expected = [
            new Row(['id' => '1', 'timestamp' => '19700101', 'unixtime' => '19700101']),
            new Row(['id' => '2', 'timestamp' => '19700101', 'unixtime' => '19700101']),
            new Row(['id' => '3', 'timestamp' => '20201129', 'unixtime' => '20201129']),
        ];
        $transformer = new FormatUnixTime();
        $transformer->options(['columns' => ['timestamp', 'unixtime'], 'timezone' => 'UTC']);
        $this->execute($transformer, $this->data);
        static::assertEquals($expected, $this->data);
    }

    /** @test */
    public function systemDefaultTimezone(): void
    {
        $expected = [
            new Row(['id' => '1', 'timestamp' => date_create()->setTimestamp(1)->format('Ymd'), 'unixtime' => '1']),
            new Row(['id' => '2', 'timestamp' => date_create()->setTimestamp(2)->format('Ymd'), 'unixtime' => '2']),
            new Row([
                'id' => '3',
                'timestamp' => date_create()->setTimestamp(1606613796)->format('Ymd'),
                'unixtime' => '1606613796',
            ]),
        ];
        $transformer = new FormatUnixTime();
        $transformer->options(['columns' => ['timestamp']]);
        $this->execute($transformer, $this->data);
        static::assertEquals($expected, $this->data);
    }
}
