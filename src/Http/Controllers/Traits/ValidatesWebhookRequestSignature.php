<?php

namespace Upcoach\UpstartForLaravel\Http\Controllers\Traits;

use Exception;
use Illuminate\Support\Str;

trait ValidatesWebhookRequestSignature
{
    /**
     * Ensures that the request signature is valid by:
     * 1. checking if the request is expired or not,
     * 2. checking if the request signature is valid or not
     *
     * @throws Exception
     */
    public function ensureRequestSignatureIsValid(array $data, array $keys): void
    {
        [$requestTime, $requestSignature] = explode(
            ',',
            Str::of($data['Upcoach-Signature'])->after('t=')
        );
        $requestTime = (int) $requestTime;
        $expiresAfterHours = 24;
        if (now()->timestamp - $requestTime > $expiresAfterHours * 3600) {
            throw new Exception('Request is expired', 401);
        }
        $requestData = collect($data)->only($keys);
        $requestData['timestamp'] = $requestTime;
        $mySignature = hash_hmac(
            'sha256',
            $requestTime.'.'.json_encode($requestData),
            config('upstart-for-laravel.signing_secret')
        );
        if ($mySignature !== $requestSignature) {
            throw new Exception('Request has invalid signature', 401);
        }
    }
}
