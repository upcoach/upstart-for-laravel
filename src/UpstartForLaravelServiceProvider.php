<?php

namespace Upcoach\UpstartForLaravel;

use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Upcoach\UpstartForLaravel\Api\Client;
use Upcoach\UpstartForLaravel\Commands\SimulateCommand;
use Upcoach\UpstartForLaravel\Mocks\FakeClient;
use Upcoach\UpstartForLaravel\Models\Installation;

class UpstartForLaravelServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('upstart-for-laravel')
            ->hasConfigFile()
            ->hasViews()
            ->hasRoute('api')
            ->hasMigration('create_installations_table')
            ->hasCommand(SimulateCommand::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->copyAndRegisterServiceProviderInApp()
                    ->askToStarRepoOnGitHub('upcoach/upstart-for-laravel');
            });

        $this->app->bind(Client::class, function ($app, $params) {
            if (config('upstart-for-laravel.development_mode')) {
                return new FakeClient();
            }

            $installation = collect($params)->first(fn ($param) => $param instanceof Installation);

            return new Client($installation);
        });
    }
}
