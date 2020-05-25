<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests\Traits;

use Tests\TestCase;
use Wizaplace\Etl\Exception\IoException;
use Wizaplace\Etl\Traits\FilePathTrait;

class FilePathTraitTest extends TestCase
{
    /** @var object * */
    protected $fakeLoader;

    public function setUp(): void
    {
        $this->fakeLoader = new class() {
            use FilePathTrait;

            public function input(string $filePath)
            {
                return $this->checkOrCreateDir($filePath);
            }
        };
    }

    /** @test */
    public function can_recursivly_create_a_dir_path()
    {
        $base = uniqid();
        $filePath = sys_get_temp_dir() . "/phpunit_$base/test/output";

        static::assertEquals(true, $this->fakeLoader->input($filePath));
    }

    /** @test */
    public function illegal_path_trigger_exception()
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
