<?php

use Upcoach\UpstartForLaravel\Api\Data\ProgramInfo;
use Upcoach\UpstartForLaravel\Api\Data\ProgramMemberInfo;
use Upcoach\UpstartForLaravel\Mocks\FakeClient;

it('gets program info', function () {
    $client = new FakeClient();

    $programId = 'program-id';

    $programInfo = $client->getProgramInfo($programId);

    expect($programInfo)->toBeInstanceOf(ProgramInfo::class);
    expect($programInfo->id)->toBe($programId);
    expect($programInfo->name)->not()->toBeNull();
});

it('gets program members', function () {
    $client = new FakeClient();

    $programId = 'program-id';

    $response = $client->getProgramMembers($programId);
    $programMembers = $response->members;

    expect($programMembers)->toBeArray();
    expect($programMembers)->not()->toBeEmpty();
    expect($programMembers[0])->toBeInstanceOf(ProgramMemberInfo::class);
    expect($programMembers[0]->id)->not()->toBeNull();
    expect($programMembers[0]->name)->not()->toBeNull();
    expect($programMembers[0]->email)->not()->toBeNull();
    expect($programMembers[0]->timezone)->not()->toBeNull();
    expect($programMembers[0]->role)->not()->toBeNull();
    expect($programMembers[0]->created_at)->not()->toBeNull();
    expect($programMembers[0]->info)->toBeArray();
});

it('gets program member info', function () {
    $client = new FakeClient();

    $programId = 'program-id';
    $userId = 'user-id';

    $programMemberInfo = $client->getProgramMemberInfo($programId, $userId);

    expect($programMemberInfo)->toBeInstanceOf(ProgramMemberInfo::class);
    expect($programMemberInfo->id)->toBe($userId);
    expect($programMemberInfo->name)->not()->toBeNull();
    expect($programMemberInfo->email)->not()->toBeNull();
    expect($programMemberInfo->timezone)->not()->toBeNull();
    expect($programMemberInfo->role)->not()->toBeNull();
    expect($programMemberInfo->created_at)->not()->toBeNull();
    expect($programMemberInfo->info)->toBeArray();
});
