<?php

use Upcoach\UpstartForLaravel\Http\Requests\InstallationRequest;

it('merges headers into request body', function () {
    $request = new InstallationRequest();

    $request->headers->set('Upcoach-Signature', 't=123');
    $request->headers->set('Upcoach-Refresh-Token', 'true');

    $method = new ReflectionMethod(InstallationRequest::class, 'prepareForValidation');
    $method->setAccessible(true);
    $method->invoke($request);

    expect($request->input('Upcoach-Signature'))->toBe('t=123');
    expect($request->input('Upcoach-Refresh-Token'))->toBe('true');
});
