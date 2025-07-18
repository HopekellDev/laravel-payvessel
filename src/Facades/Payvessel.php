<?php

namespace HopekellDev\Payvessel\Facades;

use Illuminate\Support\Facades\Facade;

class Payvessel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'payvessel';
    }
}
