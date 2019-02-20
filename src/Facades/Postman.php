<?php

namespace Jeylabs\Postman\Facades;
use Illuminate\Support\Facades\Facade;

class Postman extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'postman';
    }
}