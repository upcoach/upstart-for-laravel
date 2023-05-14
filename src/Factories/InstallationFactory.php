<?php

namespace Upcoach\UpstartForLaravel\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Upcoach\UpstartForLaravel\Models\Installation;

class InstallationFactory extends Factory
{
    protected $model = Installation::class;

    public function definition()
    {
        return [
            'organization_id' => 'org-dev-'.$this->faker->uuid,
            'token' => 'dev-'.$this->faker->uuid,
        ];
    }
}
