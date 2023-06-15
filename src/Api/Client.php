<?php

namespace Upcoach\UpstartForLaravel\Api;

use Carbon\Carbon;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Upcoach\UpstartForLaravel\Api\Data\ProgramInfo;
use Upcoach\UpstartForLaravel\Api\Data\ProgramMemberInfo;
use Upcoach\UpstartForLaravel\Models\Installation;

class Client
{
    public function __construct(protected Installation $installation)
    {
    }

    public function getProgramInfo(string $programId): ProgramInfo
    {
        $response = $this
            ->httpMacro()
            ->get("/apps/programs/$programId");

        return new ProgramInfo($response->json('program.id'), $response->json('program.name'));
    }

    public function getProgramMembers(string $programId): array
    {
        $response = $this
            ->httpMacro()
            ->get("/apps/programs/$programId/members");

        return collect($response->json('data'))
            ->map(fn (array $member) => new ProgramMemberInfo(
                $member['id'],
                $member['avatar'],
                $member['name'],
                $member['email'],
                $member['timezone'],
                $member['role'],
                Carbon::createFromTimestamp($member['created_at']),
                $member['info'] ?? [],
            ))
            ->toArray();
    }

    public function getProgramMemberInfo(string $programId, string $userId): ?ProgramMemberInfo
    {
        $response = $this
            ->httpMacro()
            ->get("/apps/programs/$programId/members?filter[id]={$userId}");

        return new ProgramMemberInfo(
            $response->json('data.0.id'),
            $response->json('data.0.avatar'),
            $response->json('data.0.name'),
            $response->json('data.0.email'),
            $response->json('data.0.timezone'),
            $response->json('data.0.role'),
            Carbon::createFromTimestamp($response->json('data.0.created_at')),
            $response->json('data.0.info') ?? [],
        );
    }

    public function httpMacro(): PendingRequest
    {
        return Http::baseUrl(config('upstart-for-laravel.api_url'))
            ->throw()
            ->withHeaders([
                'X-UP-App' => config('upstart-for-laravel.app_id'),
                'X-UP-Organization' => $this->installation->organization_id,
            ])
            ->when(app()->environment('local'), function (PendingRequest $request) {
                return $request->withoutVerifying();
            })
            ->withToken($this->installation->token);
    }
}
