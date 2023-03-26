<?php

namespace Upcoach\UpstartForLaravel\Api\Data;

use Carbon\Carbon;

class ProgramMemberInfo
{
    public function __construct(
        public readonly string $id,
        public readonly string $avatar,
        public readonly string $name,
        public readonly string $email,
        public readonly string $timezone,
        public readonly string $role,
        public readonly Carbon $created_at,
        public readonly array $info,
    ) {
    }
}
