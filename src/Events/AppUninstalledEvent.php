<?php

namespace Upcoach\UpstartForLaravel\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AppUninstalledEvent
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $installationId)
    {
    }
}
