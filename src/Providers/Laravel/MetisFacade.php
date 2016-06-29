<?php

namespace Marquine\Metis\Providers\Laravel;

use Illuminate\Support\Facades\Facade;

class MetisFacade extends Facade
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
