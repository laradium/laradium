<?php

namespace Netcore\Aven\Providers;

use Netcore\Aven\Console\Commands\MakeAvenResource;
use Netcore\Aven\Registries\FieldRegistry;
use Illuminate\Support\ServiceProvider;

class AvenServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerResources();

        $configPath = __DIR__ . '/../../config/aven.php';
        $this->mergeConfigFrom( $configPath, 'aven' );

        $this->publishes([
            $configPath => config_path('aven.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'aven');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAvenResource::class,
            ]);
        }

        $this->publishes([
            __DIR__.'/../../public/aven' => public_path('aven'),
        ], 'public');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(FieldRegistry::class, function () {
            $registry = new FieldRegistry();

            foreach (config('aven.fields_list', []) as $name => $type) {
                $registry->register($name, $type);
            }

            return $registry;
        });
    }

    /**
     * Registers all resources
     */
    protected function registerResources()
    {
        $resources = config('aven.resources', []);

        $aven = app(\Netcore\Aven\Aven\Aven::class);
        foreach ($resources as $resource) {
            $aven->register($resource);
        }
    }
}
