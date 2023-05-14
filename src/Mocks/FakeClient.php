<?php

namespace Upcoach\UpstartForLaravel\Mocks;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Upcoach\UpstartForLaravel\Api\Client;
use Upcoach\UpstartForLaravel\Models\Installation;

class FakeClient extends Client
{
    public function __construct()
    {
        $installation = Installation::factory()->make();
        parent::__construct($installation);
    }

    public function httpMacro(): PendingRequest
    {
        Http::fake(function (Request $request) {
            $caller = collect(debug_backtrace())->firstWhere('class', Client::class);
            if (! $caller) {
                return Http::response();
            }

            $method = $caller['function'];
            fake()->seed($caller['args'][0]);

            return match ($method) {
                'getProgramInfo' => Http::response([
                    'program' => [
                        'id' => $caller['args'][0],
                        'name' => fake()->name,
                    ],
                ], 200),
                'getProgramMembers' => Http::response([
                    'data' => collect(range(1, 10))->map(fn () => [
                        'id' => fake()->uuid,
                        'avatar' => fake()->imageUrl(),
                        'name' => fake()->name,
                        'email' => fake()->email,
                        'timezone' => fake()->timezone,
                        'role' => fake()->randomElement(['coach', 'member']),
                        'created_at' => fake()->unixTime,
                        'info' => [],
                    ])
                        ->map(function ($member, $index) use ($caller) {
                            if ($index !== 0) {
                                return $member;
                            }

                            $member['id'] = 'u-'.$caller['args'][0];
                            $member['role'] = 'coach';

                            return $member;
                        })->toArray(),
                ], 200),
                'getProgramMemberInfo' => Http::response([
                    'data' => [
                        [
                            'id' => $caller['args'][1],
                            'avatar' => fake()->imageUrl(),
                            'name' => fake()->name,
                            'email' => fake()->email,
                            'timezone' => fake()->timezone,
                            'role' => fake()->randomElement(['coach', 'member']),
                            'created_at' => fake()->unixTime,
                            'info' => [],
                        ],
                    ],
                ], 200),
                default => Http::response(),
            };
        });

        return parent::httpMacro();
    }
}
