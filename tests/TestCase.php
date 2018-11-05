<?php

namespace Tests;

use Marquine\Etl\Loaders\Loader;
use Marquine\Etl\Transformers\Transformer;
use PHPUnit\Framework\TestCase as BaseTestCase;

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
