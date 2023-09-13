<?php

namespace Upcoach\UpstartForLaravel\Http\Controllers;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Upcoach\UpstartForLaravel\Events\AppUninstalledEvent;
use Upcoach\UpstartForLaravel\Http\Controllers\Traits\ValidatesAppInstallationForOrganization;
use Upcoach\UpstartForLaravel\Http\Controllers\Traits\ValidatesWebhookRequestSignature;
use Upcoach\UpstartForLaravel\Http\Requests\WebhookRequest;
use Upcoach\UpstartForLaravel\Models\Installation;

class WebhookController extends Controller
{
    use ValidatesAppInstallationForOrganization;
    use ValidatesWebhookRequestSignature;

    /**
     * Handles all the incoming webhook requests
     */
    public function __invoke(WebhookRequest $request): Response
    {
        $data = $request->validated();

        try {
            $this->ensureRequestSignatureIsValid($data, ['event', 'event_timestamp', 'data']);

            $method = 'handle'.Str::studly(str_replace('.', '_', $data['event']));
            if (method_exists($this, $method)) {
                return $this->{$method}($data);
            }
        } catch (Exception $e) {
            Log::error($e);

            return response('Server error', $e->getCode());
        }

        return response('Missing method implementation', 200);
    }

    protected function handleAppUninstalled($data): Response
    {
        $this->ensureAppIsInstalledForOrganizationBefore(['organization' => $data['data']['organization'] ?? null]);
        /** @var Installation $installation */
        $installation = Installation::query()->forOrganization($data['data']['organization'])->first();
        $installation->delete();

        event(new AppUninstalledEvent($installation->id));

        return response('Uninstall request handled successfully', 200);
    }
}
