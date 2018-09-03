<?php

namespace Laradium\Laradium\Providers;

use Laradium\Laradium\Console\Commands\FindTranslations;
use Laradium\Laradium\Console\Commands\ImportTranslations;
use Laradium\Laradium\Console\Commands\MakeLaradiumResource;
use Laradium\Laradium\Helpers\Translate;
use Laradium\Laradium\Http\Middleware\LaradiumMiddleware;
use Laradium\Laradium\Registries\FieldRegistry;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class LaradiumServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerResources();
        $this->app->register(\Dimsav\Translatable\TranslatableServiceProvider::class);
        $this->app->register(LaradiumTranslationServiceProvider::class);

        \App::bind('Translate', function () {
            return new Translate;
        });

        Blade::directive('lg', function ($expression) {
            return "<?php echo lg($expression); ?>";
        });

        $this->app['router']->aliasMiddleware('laradium', LaradiumMiddleware::class);

        $this->publishes([
            __DIR__ . '/../../config/laradium-setting.php' => config_path('laradium-setting.php'),
            __DIR__ . '/../../config/laradium.php'         => config_path('laradium.php'),
            __DIR__ . '/../../config/paperclip.php'    => config_path('paperclip.php'),
        ], 'laradium');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeLaradiumResource::class,
                ImportTranslations::class,
                FindTranslations::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../../public/laradium' => public_path('laradium'),
        ], 'laradium');

        // Global helpers
        require_once __DIR__ . '/../Helpers/Global.php';

        // Mail config
        $this->setMailConfig();
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

            foreach (config('laradium.fields_list', []) as $name => $type) {
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
        $resources = config('laradium.resources', []);

        $laradium = app(\Laradium\Laradium\Base\Laradium::class);
        foreach ($resources as $resource) {
            $laradium->register($resource);
        }
    }

    /**
     * Set mail config
     *
     * @return void
     */
    private function setMailConfig()
    {
        try {
            config([
                'mail.host' => setting()->get('mail.mail_host', ''),
                'mail.port' => setting()->get('mail.mail_port', '2525'),
                'mail.username' => setting()->get('mail.mail_user', ''),
                'mail.password' => setting()->get('mail.mail_password', ''),
                'mail.from.address' => setting()->get('mail.mail_from_address', ''),
                'mail.from.name' => setting()->get('mail.mail_from_name', '')
            ]);
        } catch (\Exception $e) {}
    }
}
