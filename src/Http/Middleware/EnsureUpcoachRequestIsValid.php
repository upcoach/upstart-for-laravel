<?php

namespace Upcoach\UpstartForLaravel\Http\Middleware;

use Illuminate\Support\Facades\Validator;
use Spatie\UrlSigner\Md5UrlSigner;
use Upcoach\UpstartForLaravel\Models\Installation;

class EnsureUpcoachRequestIsValid
{
    public function handle($request, $next)
    {
        $this->validateRequest($request);
        $this->validateSignature($request);
        $this->validateInstallation($request);

        return $next($request);
    }

    private function validateRequest($request)
    {
        $validationRules = collect([
            'a' => [
                'parameter' => 'app_id',
                'rules' => ['required'],
            ],
            'b' => [
                'parameter' => 'block_id',
                'rules' => ['required'],
            ],
            'pb' => [
                'parameter' => 'program_block_id',
                'rules' => ['required'],
            ],
            'o' => [
                'parameter' => 'organization_id',
                'rules' => ['required'],
            ],
            'u' => [
                'parameter' => 'user_id',
                'rules' => ['required'],
            ],
            'p' => [
                'parameter' => 'program_id',
                'rules' => ['required'],
            ],
            'r' => [
                'parameter' => 'user_role',
                'rules' => ['nullable'],
            ],
        ])
            ->each(function ($replace, $find) use ($request) {
                $request->query->add([$replace['parameter'] => $request->query($find)]);
                $request->query->remove($find);
            })
            ->pluck('rules', 'parameter')
            ->toArray();

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            abort(422, 'Request is missing required parameters');
        }
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
