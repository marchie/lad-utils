<?php namespace Marchie\LaravelAzureDeploymentUtilities;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Marchie\LaravelAzureDeploymentUtilities\Commands\AzureConfigCacheCommand;
use Marchie\LaravelAzureDeploymentUtilities\Commands\AzureOptimizeClassesCommand;

class ServiceProvider extends LaravelServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot() {
        //
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->registerCommands();
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'command.azure.optimize-classes'
        ];
    }

    private function registerCommands()
    {
        $this->app->singleton('command.azure.optimize-classes', function ($app) {
            return new AzureOptimizeClassesCommand($app['composer']);
        });

        $this->commands('command.azure.optimize-classes');
    }
}
