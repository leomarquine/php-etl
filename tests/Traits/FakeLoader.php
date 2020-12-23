<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests\Traits;

use Wizaplace\Etl\Traits\FilePathTrait;

class FakeLoader
{
    use FilePathTrait;

    public function input(string $filePath): bool
    {
        return $this->checkOrCreateDir($filePath);
    }
}
