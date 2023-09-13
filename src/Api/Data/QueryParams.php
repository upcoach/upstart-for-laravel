<?php

namespace Upcoach\UpstartForLaravel\Api\Data;

class QueryParams
{
    public array $filters = [];

    public array $variables = [];

    public function __construct()
    {
    }

    public function addFilter(string $key, string $value): self
    {
        $this->filters[$key] = $value;

        return $this;
    }

    public function addVariable(string $key, string $value): self
    {
        $this->variables[$key] = $value;

        return $this;
    }

    public function toArray(): array
    {
        $filter = collect($this->filters)
            ->mapWithKeys(fn ($value, $key) => [$key => $value])
            ->toArray();

        $variables = collect($this->variables)
            ->mapWithKeys(fn ($value, $key) => [$key => $value])
            ->toArray();

        return array_merge($variables, compact('filter'));
    }
}
