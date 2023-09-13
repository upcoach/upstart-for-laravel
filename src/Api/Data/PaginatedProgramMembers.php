<?php

namespace Upcoach\UpstartForLaravel\Api\Data;

class PaginatedProgramMembers
{
    public function __construct(
        public readonly array $members,
        public readonly int $total,
        public readonly int $perPage,
        public readonly int $currentPage,
        public readonly int $lastPage,
    ) {
    }
}
