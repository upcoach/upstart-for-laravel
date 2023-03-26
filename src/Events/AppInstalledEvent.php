<?php

namespace Upcoach\UpstartForLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Upcoach\UpstartForLaravel\Models\Installation;

class AppInstalledEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public Installation $installation)
    {
    }
}
