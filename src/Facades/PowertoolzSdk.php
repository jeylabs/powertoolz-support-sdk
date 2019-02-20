<?php

namespace Jeylabs\PowertoolzSupportSdk\Facades;
use Illuminate\Support\Facades\Facade;

class PowertoolzSdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'PowertoolzSdk';
    }
}