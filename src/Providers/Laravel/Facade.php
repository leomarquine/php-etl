<?php

namespace Marquine\Metis\Providers\Laravel;

class Facade extends \Illuminate\Support\Facades\Facade
{
    /**
     * Get the registered name of the componsent.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'metis';
    }
}
