<?php

declare(strict_types=1);

/**
 * @author      Wizacha DevTeam <dev@wizacha.com>
 * @copyright   Copyright (c) Wizacha
 * @copyright   Copyright (c) Leonardo Marquine
 * @license     MIT
 */

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Wizaplace\Etl\Loaders\Loader;
use Wizaplace\Etl\Transformers\Transformer;

abstract class TestCase extends BaseTestCase
{
    protected function execute($step, $data)
    {
        if ($step instanceof Transformer) {
            $method = 'transform';
        }

        if ($step instanceof Loader) {
            $method = 'load';
        }

        $step->initialize();

        foreach ($data as $row) {
            $step->$method($row);
        }

        $step->finalize();
    }
}
