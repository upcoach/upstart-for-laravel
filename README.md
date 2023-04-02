# Get started developing apps quickly for the upcoach platform with this package.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/upcoach/upstart-for-laravel.svg?style=flat-square)](https://packagist.org/packages/upcoach/upstart-for-laravel)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/upcoach/upstart-for-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/upcoach/upstart-for-laravel/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/upcoach/upstart-for-laravel/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/upcoach/upstart-for-laravel/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/upcoach/upstart-for-laravel.svg?style=flat-square)](https://packagist.org/packages/upcoach/upstart-for-laravel)

This package provides a starting point for developing applications for the upcoach platform. It contains useful components, such as an API client, request validation, and webhook controllers, to help you get started smoothly.

## Features

### Installation and Webhook Management

To use this package with upcoach, you will need to provide an Installation URL and a Webhook URL in the upcoach developer portal.
This package provides two endpoints in the `routes/api.php` file to handle these URLs automatically:

- `POST /api/upcoach-install` 
- `POST /api/upcoach-webhooks:`

To set up your application, use the following URLs:

- `https://your-application-address.com/api/upcoach-install` for the Installation URL.
- `https://your-application-address.com/api/upcoach-webhooks` for the Webhook URL.

### Middleware for upcoach Requests 

To validate incoming requests from upcoach, you can use the `Upcoach\UpstartForLaravel\Http\Middleware\EnsureUpcoachRequestIsValid` middleware. This middleware will validate the signature of the request, and if it is valid, it will add the installation to the request object.

### API Client

Once the app is installed, an installation request is initiated that includes an API token. This token is saved in the database through the installation model, along with other relevant information. You can utilize this token to access the required API endpoints (scoped to the installed organization) when querying the upcoach API.

Here's how you can use the provided upcoach API client:

```php
$installation = Upcoach\UpstartForLaravel\Models\Installation::query()
    ->forOrganization($organizationId)
    ->firstOrFail();
```

Or if you are using the `Upcoach\UpstartForLaravel\Http\Middleware\EnsureUpcoachRequestIsValid` middleware, you can access the installation via the request object:

```php
$installation = $request->installation;
```

Then you can use the upcoach API client to query the API:

```php
$client = new Upcoach\UpstartForLaravel\Api\Client($installation)
$programInfo = $client->getProgramInfo($programId);
```

## Getting Started

To get started with this package, you will need to follow these steps:

- Sign up for the upcoach developer program on the [upcoach developer portal](https://developers.upcoach.com).
- Create a new application on the developer portal.
- Install the package using the following command:
```bash
composer require upcoach/upstart-for-laravel
```
- Publish the configuration and migration files by running this command:
```bash
php artisan upstart-for-laravel:install
```
- Configure your Laravel application by adding the following details to your .env file:
```bash
UPCOACH_APP_ID=
UPCOACH_APP_SIGNING_SECRET=
```
You can find these details in the developer information section of your application on the developer portal.

## Usage

- Complete the [Getting Started](#getting-started) steps above.
- Create a new block on the developer portal.
- Create a new route in your application to handle the block.
- Add the `Upcoach\UpstartForLaravel\Http\Middleware\EnsureUpcoachRequestIsValid` middleware to the route.
```php
Route::group(['middleware' => [EnsureUpcoachRequestIsValid::class]], function () {
    Route::get('/your-block-connector-path', YourBlockController::class);
});
```
- Create a new controller for the route.
```php
class YourBlockController extends Controller
{
    public function __invoke(Request $request)
    {
        /**
         * Parameters coming from the upcoach
         * app_id
         * organization_id
         * program_id
         * block_id
         * program_block_id
         * user_id
         * user_role
         */

        // You can access the installation via the request object.
        $installation = $request->installation;

        // You can use the installation to query the upcoach API if needed.
        $client = new Upcoach\UpstartForLaravel\Api\Client($installation);
        $programInfo = $client->getProgramInfo($request->program_id);

        // Your code here.
    }
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [upcoach](https://github.com/upcoach)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
