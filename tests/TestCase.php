<?php

namespace Upcoach\UpstartForLaravel\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Encryption\Encrypter;
use Orchestra\Testbench\TestCase as Orchestra;
use Upcoach\UpstartForLaravel\UpstartForLaravelServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Upcoach\\UpstartForLaravel\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function getPackageProviders($app)
    {
        return [
            UpstartForLaravelServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');
        config()->set('app.key', Encrypter::generateKey(config('app.cipher')));
        config()->set('upstart-for-laravel.app_id', fake()->uuid);
        config()->set('upstart-for-laravel.signing_secret', fake()->uuid);

        $migration = include __DIR__.'/../database/migrations/create_installations_table.php.stub';
        $migration->up();
    }
}
