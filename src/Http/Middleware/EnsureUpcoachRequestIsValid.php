<?php

namespace Upcoach\UpstartForLaravel\Http\Middleware;

use Spatie\UrlSigner\Md5UrlSigner;
use Upcoach\UpstartForLaravel\Models\Installation;

class EnsureUpcoachRequestIsValid
{
    public function handle($request, $next)
    {
        $this->renameParameters($request);
        $this->validateSignature($request);
        $this->validateInstallation($request);

        $response = $next($request);
        if (config('upstart-for-laravel.allow_all_domains_as_ancestors_on_blocks')) {
            $response->headers->set('Content-Security-Policy', 'frame-ancestors https://*');
        }

        return $response;
    }

    private function renameParameters($request)
    {
        collect([
            'a' => 'app_id',
            'o' => 'organization_id',
            'p' => 'program_id',
            'b' => 'block_id',
            'pb' => 'program_block_id',
            'u' => 'user_id',
            'r' => 'user_role',
        ])
            ->each(function ($replace, $find) use ($request) {
                $request->query->add([$replace => $request->query($find)]);
                $request->query->remove($find);
            });
    }

    private function validateSignature($request)
    {
        $urlSigner = new MD5UrlSigner(config('upstart-for-laravel.signing_secret'));
        if (! $urlSigner->validate($request->fullUrl())) {
            abort(401, 'Request has invalid signature');
        }
    }

    private function validateInstallation($request)
    {
        $installation = Installation::query()->forOrganization($request->organization_id)->first();
        if (! $installation) {
            abort(422, "App is not installed yet for organization: $request->organization_id");
        }

        $request->merge(['installation' => $installation]);
    }
}
