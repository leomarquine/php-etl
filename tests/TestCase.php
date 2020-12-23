<?php

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Step;
use Wizaplace\Etl\Transformers\Transformer;

abstract class TestCase extends BaseTestCase
{
    protected function execute(Step $step, array $data): void
    {
        if ($step instanceof Transformer) {
            $method = 'transform';
        }

        if ($step instanceof Loader) {
            $method = 'load';
        }

        $step->initialize();

        if (isset($method)) {
            foreach ($data as $row) {
                $step->$method($row);
            }
        }

        $step->finalize();
    }
}
