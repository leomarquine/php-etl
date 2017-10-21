<?php

namespace Tests;

use Tests\TestCase;

class EtlTest extends TestCase
{
    /** @test */
    function get_and_set_configuration_options()
    {
        $this->assertNull(Etl::get('nested.key'));

        Etl::set('nested.key', 'value');

        $this->assertEquals(['key' => 'value'], Etl::get('nested'));
        $this->assertEquals('value', Etl::get('nested.key'));
    }

    /** @test */
    function add_connection()
    {
        $this->assertNull(Etl::get('connections.default'));
        $this->assertNull(Etl::get('connections.mysql'));

        Etl::addConnection(['driver' => 'pgsql']);
        Etl::addConnection(['driver' => 'mysql'], 'mysql');

        $this->assertEquals(['driver' => 'pgsql'], Etl::get('connections.default'));
        $this->assertEquals(['driver' => 'mysql'], Etl::get('connections.mysql'));
    }
}

class Etl extends \Marquine\Etl\Etl {
    protected static $config = [];
}
