<?php

namespace Upcoach\UpstartForLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Upcoach\UpstartForLaravel\Mocks\FakeRequest;

class SimulateCommand extends Command
{
    public $signature = 'upstart:simulate {--T|type= : The type of event to simulate} {--U|url=} {--P|payload= : The payload to send}';

    public $description = 'Simulate an app installation';

    public function handle()
    {
        $type = $this->option('type');

        if (! $type) {
            $type = $this->choice('What type of event do you want to simulate?', [
                'block',
                'settings',
            ], 'block');
        }

        $url = $this->option('url');
        if (! $url) {
            $url = $this->ask('What is the simulation URL?');
        }

        if (! $url) {
            $this->error('You must provide a URL');

            return;
        }

        if (! Str::startsWith($url, '/')) {
            $url = url($url);
        }

        $payload = $this->option('payload') ?? [];
        if ($payload) {
            $payload = json_decode($payload, true) ?? [];
        }

        $fakeRequest = new FakeRequest();
        [$response, $payload] = match ($type) {
            'block' => $fakeRequest->block($url, $payload),
            'settings' => $fakeRequest->settings($url, $payload),
        };

        $this->comment('You can run this command directly from the console:');
        $this->line('php artisan upstart:simulate --type='.$type.' --url='.$url.($payload ? ' --payload=\''.json_encode($payload).'\'' : ''));
        $this->newLine();

        $this->info($response);
    }
}
