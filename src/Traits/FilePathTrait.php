<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Wizaplace\Etl\Traits;

use Wizaplace\Etl\Exception\IoException;

trait FilePathTrait
{
    /**
     * Check if path dirname exist,
     * recursivly create dir path if not
     *
     * @throws IoException
     */
    protected function checkOrCreateDir(string $fileUri): bool
    {
        $dirName = dirname($fileUri);

        if (is_dir($dirName)) {
            return true;
        }

        $isCreated = @\mkdir($dirName, 0755, true);
        if (false === $isCreated) {
            throw new IoException(
                "Cannot create path: $dirName",
                1
            );
        }

        return $isCreated;
    }
}
