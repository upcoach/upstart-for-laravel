<?php

use Illuminate\Http\Request;
use Upcoach\UpstartForLaravel\Http\Middleware\EnsureUpcoachRequestIsValid;
use Upcoach\UpstartForLaravel\Mocks\FakeRequest;
use Upcoach\UpstartForLaravel\Models\Installation;

beforeEach(function () {
    $this->middleware = new EnsureUpcoachRequestIsValid();
});

it('renames request parameters', function () {
    [$request, $payload] = fakeRequest();

    $this->middleware->handle($request, function ($request) use ($payload) {
        expect($request->query('app_id'))->toBe($payload['a']);
        expect($request->query('organization_id'))->toBe($payload['o']);
        expect($request->query('program_id'))->toBe($payload['p']);
        expect($request->query('block_id'))->toBe($payload['b']);
        expect($request->query('program_block_id'))->toBe($payload['pb']);
        expect($request->query('user_id'))->toBe($payload['u']);
        expect($request->query('user_role'))->toBe($payload['r']);

        return response(null);
    });
});

it('aborts with 401 if request signature is invalid', function () {
    $request = Request::create(url('test'), 'GET');

    try {
        $this->middleware->handle($request, fn () => null);
    } catch (Exception $e) {
        expect($e->getMessage())->toBe('Request has invalid signature');
        expect($e->getStatusCode())->toBe(401);
    }
});

it('aborts with 422 if app is not installed for the organization', function () {
    [$request, $payload] = fakeRequest(['o' => 'not-installed']);

    try {
        $this->middleware->handle($request, fn () => response(''));
    } catch (Exception $e) {
        expect($e->getMessage())->toContain('App is not installed yet for organization');
        expect($e->getStatusCode())->toBe(422);
    }
});

it('adds installation to the request if app is installed for the organization', function () {
    [$request, $payload] = fakeRequest();

    $this->middleware->handle($request, function ($request) use ($payload) {
        expect($request->installation)->toEqual(Installation::query()->forOrganization($payload['o'])->first());

        return response(null);
    });
});

it('adds csp header if allow_all_domains_as_ancestors_on_blocks is true', function () {
    config(['upstart-for-laravel.allow_all_domains_as_ancestors_on_blocks' => true]);

    [$request, $payload] = fakeRequest();
    $response = $this->middleware->handle($request, function ($request) {
        return response(null);
    });

    expect($response->headers->get('Content-Security-Policy'))->toBe('frame-ancestors https://*');
});

function fakeRequest($payload = [])
{
    [$url, $payload] = (new FakeRequest())->block(url('test'), $payload);
    $queryParams = parse_url($url)['query'];
    parse_str($queryParams, $queryParams);

    return [
        Request::create($url, 'GET', $queryParams),
        $payload,
    ];
}
