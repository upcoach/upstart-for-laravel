<?php

use Upcoach\UpstartForLaravel\Events\AppUninstalledEvent;
use Upcoach\UpstartForLaravel\Mocks\FakeRequest;
use Upcoach\UpstartForLaravel\Models\Installation;

beforeEach(function () {
    Event::fake();

    $this->request = new FakeRequest();
});

it('handles app uninstalled event', function () {
    $installation = Installation::factory()->create();
    [$data, $headers] = $this->request->webhook([
        'event' => 'app.uninstalled',
        'event_timestamp' => now()->timestamp,
        'data' => [
            'organization' => $installation->organization_id,
        ],
    ]);

    $this->postJson(route('upcoach.webhooks'), $data, $headers)
        ->assertOk()
        ->assertSee('Uninstall request handled successfully');

    expect(Installation::where('id', $installation->id)->exists())->toBeFalse();

    Event::assertDispatched(AppUninstalledEvent::class, function ($event) use ($installation) {
        return $event->installationId === $installation->id;
    });
});

it('throws exception if app is not installed before', function () {
    [$data, $headers] = $this->request->webhook([
        'event' => 'app.uninstalled',
        'event_timestamp' => now()->timestamp,
        'data' => [
            'organization' => fake()->uuid,
        ],
    ]);

    $this->postJson(route('upcoach.webhooks'), $data, $headers)
        ->assertStatus(422)
        ->assertSee('Server error');
});
