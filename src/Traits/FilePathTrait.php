<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @license     MIT
 */

declare(strict_types=1);

namespace Wizaplace\Etl\Traits;

use Wizaplace\Etl\Exception\IoException;

trait FilePathTrait
{
    /**
     * Check if path dirname exist,
     * recursively create dir path if not
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
            throw new IoException("Cannot create path: $dirName", 1);
        }

        return $isCreated;
    }

    public function getFileUri(
        string $path,
        int $linePerFile,
        int $fileCounter
    ): string {
        if (0 >= $linePerFile) {
            return $path;
        }

        $pathinfo = \pathinfo($path);

        if (\array_key_exists('extension', $pathinfo)) {
            $extension = ".{$pathinfo['extension']}";
        } else {
            $extension = '';
        }

        return
            $pathinfo['dirname']
            . DIRECTORY_SEPARATOR
            . $pathinfo['filename']
            . "_$fileCounter"
            . $extension
        ;
    }
}
