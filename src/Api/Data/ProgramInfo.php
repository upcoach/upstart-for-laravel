<?php

namespace Upcoach\UpstartForLaravel\Api\Data;

class ProgramInfo
{
    public function __construct(public readonly string $id, public readonly string $name)
    {
    }
}
