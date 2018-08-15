<?php

namespace Netcore\Aven\Providers;

use Netcore\Aven\Console\Commands\MakeAvenResource;
use Netcore\Aven\Helpers\Translate;
use Netcore\Aven\Http\Middleware\AvenMiddleware;
use Netcore\Aven\Registries\FieldRegistry;
use Illuminate\Support\ServiceProvider;

class AvenServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerResources();
        $this->app->register(\Dimsav\Translatable\TranslatableServiceProvider::class);
        $this->app->register(AvenTranslationServiceProvider::class);
        \App::bind('Translate', function () {

            return new Translate;

        });
        $this->app['router']->aliasMiddleware('aven', AvenMiddleware::class);

        $configPath = __DIR__ . '/../../config/aven.php';
        $this->mergeConfigFrom($configPath, 'aven');

        $this->publishes([
            $configPath => config_path('aven.php'),
        ], 'aven');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'aven');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAvenResource::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../../public/aven' => public_path('aven'),
        ], 'aven');

        // Global helpers
        require_once __DIR__ . '/../Helpers/Global.php';
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
