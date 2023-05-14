<?php

use Illuminate\Support\Facades\Event;
use Upcoach\UpstartForLaravel\Events\AppInstalledEvent;
use Upcoach\UpstartForLaravel\Mocks\FakeRequest;
use Upcoach\UpstartForLaravel\Models\Installation;

beforeEach(function () {
    Event::fake();

    $this->request = new FakeRequest();
});

it('creates an installation when installation webhook is received', function () {
    [$data, $headers] = $this->request->installationWebhook();

    $this->postJson(route('upcoach.install'), $data, $headers)->assertStatus(201);

    $savedInstallation = Installation::where('organization_id', $data['organization'])->first();
    expect($savedInstallation)->not()->toBeNull();
    expect($savedInstallation->token)->toEqual($data['token']);

    Event::assertDispatched(AppInstalledEvent::class, function ($event) use ($data) {
        return $event->installation->organization_id === $data['organization'];
    });
});

it('throws an exception when installation webhook is received for an already installed organization', function () {
    [$data, $headers] = $this->request->installationWebhook();

    Installation::new($data['organization'], $data['token']);

    $this->postJson(route('upcoach.install'), $data, $headers)
        ->assertStatus(422)
        ->assertJson(['message' => "App is already installed for given organization: {$data['organization']}"]);
});

it('throws an exception when installation webhook is received with invalid signature', function () {
    [$data, $headers] = $this->request->installationWebhook();
    $headers['Upcoach-Signature'] = $headers['Upcoach-Signature'].'invalid';

    $response = $this->postJson(route('upcoach.install'), $data, $headers)
        ->assertStatus(401)
        ->assertJson(['message' => 'Request has invalid signature']);
});

it('throws an exception when installation webhook is received with expired timestamp', function () {
    [$data, $headers] = $this->request->installationWebhook();
    $headers['Upcoach-Signature'] = 't=0,invalid';

    $this->postJson(route('upcoach.install'), $data, $headers)
        ->assertStatus(401)
        ->assertJson(['message' => 'Request is expired']);
});
