<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Traits;

use Tests\TestCase;
use Wizaplace\Etl\Exception\IoException;

class FilePathTraitTest extends TestCase
{
    /** @var object */
    protected $fakeLoader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->fakeLoader = new FakeLoader();
    }

    /** @test */
    public function canRecursivlyCreateDirPath(): void
    {
        $base = uniqid();
        $filePath = sys_get_temp_dir() . "/phpunit_$base/test/output";

        static::assertEquals(true, $this->fakeLoader->input($filePath));
    }

    /** @test */
    public function illegalPathTriggerException(): void
    {
        $filePath = '/dev/random/illegal/path';

        $this->expectException(IoException::class);
        $this->expectExceptionMessage(
            sprintf(
                'Cannot create path: %s',
                dirname($filePath)
            )
        );

        $this->fakeLoader->input($filePath);
    }

    /** @dataProvider provideGetFileUriData */
    public function testGetFileUri(
        string $expected,
        string $output,
        int $linePerFile,
        int $fileCounter
    ): void {
        static::assertEquals(
            $expected,
            $this->fakeLoader->getFileUri(
                $output,
                $linePerFile,
                $fileCounter
            )
        );
    }

    public function provideGetFileUriData(): array
    {
        return [
            'Unique file without extension' => [
                'expected' => 'relative/path/to/a/file',
                'output' => 'relative/path/to/a/file',
                'linePerFile' => -1,
                'fileCounter' => 1,
            ],
            'Unique file with extension' => [
                'expected' => '/hello/world.tsv',
                'output' => '/hello/world.tsv',
                'linePerFile' => 0,
                'fileCounter' => 1,
            ],
            'Multiple files with extension' => [
                'expected' => '/bye/people_42',
                'output' => '/bye/people',
                'linePerFile' => 1,
                'fileCounter' => 42,
            ],
            'Multiple relative path files without extension' => [
                'expected' => './AFILE_42.CSV',
                'output' => 'AFILE.CSV',
                'linePerFile' => 2,
                'fileCounter' => 42,
            ],
        ];
    }
}
