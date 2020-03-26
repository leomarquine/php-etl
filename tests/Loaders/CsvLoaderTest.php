<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Loaders;

use Tests\TestCase;
use Wizaplace\Etl\Etl;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Extractors\Csv;
use Wizaplace\Etl\Loaders\CsvLoader;
use Wizaplace\Etl\Loaders\Insert;
use Wizaplace\Etl\Row;

class CsvLoaderTest extends TestCase
{
    const OUTPUT_FILE = __DIR__ . '/../data/testOutput';

    /**
     * @var CsvLoader
     */
    private $csvLoader;

    public function setUp(): void
    {
        $this->csvLoader = new CsvLoader();
        $this->csvLoader->output(self::OUTPUT_FILE);
        $this->csvLoader->initialize();
    }

    public function testLoadCsvWrongPath(): void
    {
        static::expectException(IoException::class);

        $this->csvLoader = new CsvLoader();
        $this->csvLoader->output('unknown_path/unknown_file');
        $this->csvLoader->initialize();
    }

    /**
     * Test CSV loading with 2 rows and no options
     */
    public function testLoadCsvNoOption(): void
    {
        $row1 = $this->productRowFactory('Table',10.5,'A simple table');
        $row2 = $this->productRowFactory('Chair',305.75,'A \"deluxe chair\". You need it!');

        $this->csvLoader->load($row1);
        $this->csvLoader->load($row2);
        $this->csvLoader->finalize();

        // Opening generated file
        $handle = fopen(self::OUTPUT_FILE . '_0.csv', 'r');

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
        $row1 = $this->productRowFactory('Table',10.5,'A simple table');
        $row2 = $this->productRowFactory('Chair',305.75,'A #|deluxe chair#|. You need it!');

        // Custom options
        $this->csvLoader->options(['delimiter' => ',', 'enclosure' => '|', 'escapeChar' => '#']);
        $this->csvLoader->load($row1);
        $this->csvLoader->load($row2);
        $this->csvLoader->finalize();

        // Opening generated file
        $handle = fopen(self::OUTPUT_FILE . '_0.csv', 'r');

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
        $row1 = $this->productRowFactory('Table',10.5,'A simple table');
        $row2 = $this->productRowFactory('Chair',305.75,'A "deluxe chair". You need it!');
        $row3 = $this->productRowFactory('Desk',12.2,'Basic, really boring.');

        // 1 line per file
        $this->csvLoader->options(['linePerFile' => 1]);
        $this->csvLoader->load($row1);
        $this->csvLoader->load($row2);
        $this->csvLoader->load($row3);
        $this->csvLoader->finalize();

        $expectedResults = [
            'Table;10.5;"A simple table"',
            'Chair;305.75;"A ""deluxe chair"". You need it!"',
            'Desk;12.2;"Basic, really boring."'
        ];

        // We should have 3 files
        for ($i = 0; $i < 3; $i++) {
            $handle = fopen(self::OUTPUT_FILE . '_' . $i . '.csv', 'r');

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
