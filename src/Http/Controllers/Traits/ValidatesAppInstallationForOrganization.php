<?php

namespace Upcoach\UpstartForLaravel\Http\Controllers\Traits;

use Exception;
use Upcoach\UpstartForLaravel\Models\Installation;

trait ValidatesAppInstallationForOrganization
{
    /**
     * Ensures that the app is not installed for the organization before.
     *
     * @throws Exception
     */
    protected function ensureAppIsNotInstalledForOrganizationBefore(array $data): void
    {
        $organizationId = $data['organization'];
        $isInstalledBefore = Installation::query()->forOrganization($organizationId)->exists();
        if ($isInstalledBefore) {
            throw new Exception("App is already installed for given organization: $organizationId", 422);
        }
    }

    /**
     * Ensures that the app is installed for the organization before.
     *
     * @throws Exception
     */
    protected function ensureAppIsInstalledForOrganizationBefore(array $data): void
    {
        $organizationId = $data['organization'];
        $isInstalledBefore = Installation::query()->forOrganization($organizationId)->exists();
        if (! $isInstalledBefore) {
            throw new Exception("App is not installed yet for organization: $organizationId", 422);
        }
    }
}
