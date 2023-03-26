<?php

namespace Upcoach\UpstartForLaravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Upcoach\UpstartForLaravel\UpstartForLaravel
 */
class UpstartForLaravel extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Upcoach\UpstartForLaravel\UpstartForLaravel::class;
    }
}
