<?php

namespace Tests;

use Marquine\Etl\Etl;
use PHPUnit_Framework_TestCase;

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $config = [
            'path' => __DIR__ . '/data',
            'database' => [
                'default' => 'primary',
                'connections' => [
                    'primary' => [
                        'driver' => 'sqlite',
                        'database' => __DIR__ . '/data/primary.sqlite',
                    ],
                    'secondary' => [
                        'driver' => 'sqlite',
                        'database' => __DIR__ . '/data/secondary.sqlite',
                    ],
                ],
            ],
        ];

        Etl::config($config);
    }
}
