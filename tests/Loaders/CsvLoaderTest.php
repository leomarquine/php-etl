<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Loaders;

use Tests\TestCase;
use Wizaplace\Etl\Loaders\CsvLoader;
use Wizaplace\Etl\Row;

class CsvLoaderTest extends TestCase
{
    /** @var string|false|mixed */
    protected $outputPath;

    private CsvLoader $csvLoader;

    protected function setUp(): void
    {
        parent::setUp();

        $path = tempnam('/tmp', 'phpunit_');
        if (false === $path) {
            static::fail('Could not create temp file');
        }
        $this->outputPath = "{$path}.csv";

        $this->csvLoader = new CsvLoader();
        $this->csvLoader->output($this->outputPath);
        $this->csvLoader->initialize();
    }

    /**
     * Test CSV loading with 2 rows and no options
     */
    public function testLoadCsvNoOption(): void
    {
        $row1 = $this->productRowFactory('Table', 10.5, 'A simple table');
        $row2 = $this->productRowFactory('Chair', 305.75, 'A \"deluxe chair\". You need it!');

        $this->csvLoader->load($row1);
        $this->csvLoader->load($row2);
        $this->csvLoader->finalize();

        // Opening generated file
        $handle = fopen($this->outputPath, 'r');

        $line = fgets($handle);
        static::assertEquals('"Product name";Price;Description', trim($line));

        $line = fgets($handle);
        static::assertEquals('Table;10.5;"A simple table"', trim($line));

        $line = fgets($handle);
        static::assertEquals('Chair;305.75;"A \"deluxe chair\". You need it!"', trim($line));
    }

    /**
     * Test CSV loading with 2 rows and custom options
     */
    public function testLoadCsvCustomOptions(): void
    {
        $row1 = $this->productRowFactory('Table', 10.5, 'A simple table');
        $row2 = $this->productRowFactory('Chair', 305.75, 'A #|deluxe chair#|. You need it!');

        // Custom options
        $this->csvLoader->options(
            [
                $this->csvLoader::DELIMITER => ',',
                $this->csvLoader::ENCLOSURE => '|',
                $this->csvLoader::ESCAPE_CHAR => '#',
            ]
        );
        $this->csvLoader->load($row1);
        $this->csvLoader->load($row2);
        $this->csvLoader->finalize();

        // Opening generated file
        $handle = fopen($this->outputPath, 'r');

        $line = fgets($handle);
        static::assertEquals('|Product name|,Price,Description', trim($line));

        $line = fgets($handle);
        static::assertEquals('Table,10.5,|A simple table|', trim($line));

        $line = fgets($handle);
        static::assertEquals('Chair,305.75,|A #|deluxe chair#|. You need it!|', trim($line));
    }

    /**
     * Test CSV loading with 3 rows and 1 row per file
     */
    public function testLoadCsvMultipleFiles(): void
    {
        // 1 line per file
        $this->csvLoader->options([$this->csvLoader::LINE_PER_FILE => 1]);
        $this->csvLoader->initialize();

        \array_map(
            function (Row $row): void {
                $this->csvLoader->load($row);
            },
            [
                $this->productRowFactory('Table', 10.50, 'A simple table'),
                $this->productRowFactory('Chair', 305.75, 'A "deluxe chair". You need it!'),
                $this->productRowFactory('Desk', 12.2, 'Basic, really boring.'),
            ]
        );

        $this->csvLoader->finalize();

        $expectedResults = [
            1 => 'Table;10.5;"A simple table"',
            2 => 'Chair;305.75;"A ""deluxe chair"". You need it!"',
            3 => 'Desk;12.2;"Basic, really boring."',
        ];

        // We should have 3 files
        for ($i = 1; $i <= 3; $i++) {
            $handle = fopen(
                $this->csvLoader->getFileUri($this->outputPath, 1, $i),
                'r'
            );

            $line = fgets($handle);
            static::assertEquals('"Product name";Price;Description', trim($line));

            $line = fgets($handle);
            static::assertEquals($expectedResults[$i], trim($line));
        }
    }

    /**
     * Returning new row for testing
     */
    private function productRowFactory(string $name, float $price, string $description): Row
    {
        return new Row(
            [
                'Product name' => $name,
                'Price' => $price,
                'Description' => $description,
            ]
        );
    }
}
