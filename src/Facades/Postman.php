<?php

namespace Jeylabs\PowertoolzSupportSdk\Facades;
use Illuminate\Support\Facades\Facade;

class PowertoolzSupportSdk extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'powertoolz-support-sdk';
    }
}