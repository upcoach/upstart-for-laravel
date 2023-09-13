<?php

namespace Upcoach\UpstartForLaravel\Mocks;

use Spatie\UrlSigner\Md5UrlSigner;
use Upcoach\UpstartForLaravel\Models\Installation;

class FakeRequest
{
    public function installationWebhook(string $organizationId = null)
    {
        $installation = Installation::factory()->make();
        $requestTime = now()->timestamp;

        $requestData = [
            'app' => $this->appId(),
            'organization' => $organizationId ?? $installation->organization_id,
            'token' => $installation->token,
            'timestamp' => $requestTime,
        ];

        $signature = hash_hmac(
            'sha256',
            "{$requestTime}.".json_encode($requestData),
            $this->signingSecret()
        );
        $signatureHeader = "t={$requestTime},{$signature}";

        return [
            $requestData,
            [
                'Upcoach-Signature' => $signatureHeader,
                'Upcoach-Refresh-Token' => false,
            ],
        ];
    }

    public function webhook($data)
    {
        $requestTime = now()->timestamp;
        $requestData = array_merge($data, ['timestamp' => $requestTime]);

        $signature = hash_hmac(
            'sha256',
            "{$requestTime}.".json_encode($requestData),
            $this->signingSecret()
        );

        return [
            $requestData,
            [
                'Upcoach-Signature' => "t={$requestTime},{$signature}",
                'Upcoach-Refresh-Token' => false,
            ],
        ];
    }

    public function block(string $url, array $payload = []): array
    {
        $programID = $payload['p'] ?? fake()->uuid;
        fake()->seed($programID);
        $payload = [
            'a' => $payload['a'] ?? $this->appId(),
            'b' => $payload['b'] ?? fake()->uuid,
            'o' => $payload['o'] ?? Installation::first()?->organization_id ?? Installation::factory()->create()->organization_id,
            'u' => $payload['u'] ?? 'u-'.$programID,
            'p' => $programID,
            'pb' => $payload['pb'] ?? fake()->uuid,
            'r' => $payload['r'] ?? 'coach',
        ];

        return [
            $this->generateSignedConnectorUrl(
                $url,
                $this->signingSecret(),
                $payload
            ),
            $payload,
        ];
    }

    public function settings(string $url, array $payload = []): array
    {
        $organizationId = $payload['o'] ?? Installation::first()?->organization_id ?? Installation::factory()->create()->organization_id;
        fake()->seed($organizationId);
        $payload = [
            'a' => $payload['a'] ?? $this->appId(),
            'o' => $organizationId,
            'u' => $payload['u'] ?? 'u-'.$organizationId,
        ];

        return [
            $this->generateSignedConnectorUrl(
                $url,
                $this->signingSecret(),
                $payload
            ),
            $payload,
        ];
    }

    private function signingSecret(): string
    {
        return config('upstart-for-laravel.signing_secret') ?? fake()->sha256;
    }

    private function appId(): string
    {
        return config('upstart-for-laravel.app_id') ?? fake()->uuid;
    }

    private function generateSignedConnectorUrl(string $url, string $secret, array $args): string
    {
        $urlSigner = new MD5UrlSigner($secret);
        $expirationDate = now()->addMinutes(60);

        ksort($args);

        $url .= '?'.http_build_query($args);

        return $urlSigner->sign($url, $expirationDate);
    }
}
