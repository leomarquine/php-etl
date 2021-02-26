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

        static::expectException(IoException::class);
        static::expectExceptionMessage(
            sprintf(
                'Cannot create path: %s',
                dirname($filePath)
            )
        );

        $this->fakeLoader->input($filePath);
    }
}
