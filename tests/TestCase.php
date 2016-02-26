<?php
namespace Tests;

use Marchie\LaravelAzureDeploymentUtilities\ServiceProvider;

abstract class TestCase extends \Illuminate\Foundation\Testing\TestCase
{
    /**
     * Boots the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../vendor/laravel/laravel/bootstrap/app.php';

        $app->register(ServiceProvider::class);

        $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

        return $app;
    }
}