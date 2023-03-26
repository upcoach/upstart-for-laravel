<?php

namespace Upcoach\UpstartForLaravel\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Upcoach\UpstartForLaravel\UpstartForLaravelServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Upcoach\\UpstartForLaravel\\Database\\Factories\\'.class_basename($modelName).'Factory'
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

        /*
        $migration = include __DIR__.'/../database/migrations/create_upstart-for-laravel_table.php.stub';
        $migration->up();
        */
    }
}
