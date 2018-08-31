<?php

namespace Netcore\Aven\Providers;

use Netcore\Aven\Console\Commands\FindTranslations;
use Netcore\Aven\Console\Commands\ImportTranslations;
use Netcore\Aven\Console\Commands\MakeAvenResource;
use Netcore\Aven\Helpers\Translate;
use Netcore\Aven\Http\Middleware\AvenMiddleware;
use Netcore\Aven\Registries\FieldRegistry;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

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

        Blade::directive('lg', function ($expression) {
            return "<?php echo lg($expression); ?>";
        });

        $this->app['router']->aliasMiddleware('aven', AvenMiddleware::class);

        $this->publishes([
            __DIR__ . '/../../config/aven-setting.php' => config_path('aven-setting.php'),
            __DIR__ . '/../../config/aven.php'         => config_path('aven.php'),
            __DIR__ . '/../../config/paperclip.php'    => config_path('paperclip.php'),
        ], 'aven');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');

        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'aven');
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeAvenResource::class,
                ImportTranslations::class,
                FindTranslations::class,
            ]);
        }

        $this->publishes([
            __DIR__ . '/../../public/aven' => public_path('aven'),
        ], 'aven');

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
            $aven->registerApi($resource);
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
