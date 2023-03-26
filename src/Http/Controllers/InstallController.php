<?php

namespace Upcoach\UpstartForLaravel\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Upcoach\UpstartForLaravel\Events\AppInstallationTokenRefreshedEvent;
use Upcoach\UpstartForLaravel\Events\AppInstalledEvent;
use Upcoach\UpstartForLaravel\Http\Controllers\Traits\ValidatesAppInstallationForOrganization;
use Upcoach\UpstartForLaravel\Http\Controllers\Traits\ValidatesWebhookRequestSignature;
use Upcoach\UpstartForLaravel\Http\Requests\InstallationRequest;
use Upcoach\UpstartForLaravel\Models\Installation;

class InstallController extends Controller
{
    use ValidatesAppInstallationForOrganization;
    use ValidatesWebhookRequestSignature;

    /**
     * Installs the app or refreshes the token for organization
     */
    public function __invoke(InstallationRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $this->ensureRequestSignatureIsValid($data, ['app', 'organization', 'token']);

            (bool) $data['Upcoach-Refresh-Token']
                ? $this->refreshAppToken($data)
                : $this->installApp($data);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], $e->getCode());
        }

        return response()->json(null, 201);
    }

    /**
     * Installs app
     *
     * @throws Exception
     */
    protected function installApp(array $data): void
    {
        $this->ensureAppIsNotInstalledForOrganizationBefore($data);

        $installation = Installation::new($data['organization'], $data['token']);

        try {
            // For a successful installation, we have to return with
            // a 201 response code. In order to ensure that,
            // we don't allow exceptions thrown via event handlers
            // to break the flow.
            event(new AppInstalledEvent($installation));
        } catch (Exception) {
        }
    }

    /**
     * Refreshes an existing installations token
     *
     * @throws Exception
     */
    protected function refreshAppToken(array $data): void
    {
        $this->ensureAppIsInstalledForOrganizationBefore($data);

        /** @var Installation $installation */
        $installation = Installation::query()
            ->forOrganization($data['organization'])
            ->first();

        $installation->update(['token' => $data['token']]);

        try {
            // For a successful refresh, we have to return with
            // a 201 response code. In order to ensure that,
            // we don't allow exceptions thrown via event handlers
            // to break the flow.
            event(new AppInstallationTokenRefreshedEvent($installation));
        } catch (Exception) {
        }
    }
}
