<?php

namespace Laradium\Laradium\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laradium\Laradium\Console\Commands\FindTranslations;
use Laradium\Laradium\Console\Commands\ImportTranslations;
use Laradium\Laradium\Console\Commands\MakeLaradiumApiResource;
use Laradium\Laradium\Console\Commands\MakeLaradiumResource;
use Laradium\Laradium\Helpers\Translate;
use Laradium\Laradium\Http\Middleware\LaradiumMiddleware;
use Laradium\Laradium\Registries\FieldRegistry;

class LaradiumServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function boot()
    {
        $this->registerPaperClipConfig();
        $this->registerResources();
        $this->registerProviders();
        $this->registerBindings();
        $this->registerDirectives();
        $this->registerMiddleware();
        $this->registerCommands();
        $this->publishConfig();
        $this->publishAssets();
        $this->loadMigrations();
        $this->loadViews();
        $this->loadRoutes();
        $this->registerViewComposers();

        // Global helpers
        require_once __DIR__ . '/../Helpers/Global.php';

        // Mail config
        $this->setMailConfig();

        $this->setTranslatableConfig();
    }

    /**
     * @return void
     */
    private function registerProviders()
    {
        $this->app->register(\Dimsav\Translatable\TranslatableServiceProvider::class);
        $this->app->register(LaradiumTranslationServiceProvider::class);
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

            foreach ($this->getFieldList() as $name => $path) {
                $registry->register($name, $path);
            }

            return $registry;
        });
    }

    /**
     * @return void
     */
    private function registerPaperClipConfig()
    {
        $disk = config('paperclip.storage.disk', 'public') == 'paperclip' ? 'public' : config('paperclip.storage.disk',
            'public');
        $url = $disk == 'paperclip' ? config('app.url') . '/storage' : config('paperclip.storage.base-urls.public');
        config([
            'paperclip.storage.disk'             => $disk,
            'paperclip.storage.base-urls.public' => $url,
        ]);
    }

    /**
     * @return array
     */
    private function getFieldList()
    {
        $fieldPath = base_path('vendor/laradium/laradium/src/Base/Fields');
        $contentFieldPath = base_path('vendor/laradium/laradium-content/src/Base/Fields');
        $customFields = config('laradium.custom_field_directory', app_path('Laradium/Fields'));

        $fieldList = [];
        if (file_exists($fieldPath)) {
            foreach (\File::allFiles($fieldPath) as $path) {
                $field = $path->getPathname();
                $baseName = basename($field, '.php');
                $field = 'Laradium\\Laradium\\Base\\Fields\\' . $baseName;
                $fieldList[lcfirst($baseName)] = $field;
            }

            if (file_exists($contentFieldPath)) {
                foreach (\File::allFiles($contentFieldPath) as $path) {
                    $field = $path->getPathname();
                    $baseName = basename($field, '.php');
                    $field = 'Laradium\\Laradium\\Content\\Base\\Fields\\' . $baseName;
                    $fieldList[lcfirst($baseName)] = $field;
                }
            }

            if (file_exists($customFields)) {
                foreach (\File::allFiles($customFields) as $path) {
                    $field = $path->getPathname();
                    $baseName = basename($field, '.php');
                    $field = config('laradium.custom_field_namespace') . '\\' . $baseName;
                    $fieldList[lcfirst($baseName)] = $field;
                }
            }
        }

        return $fieldList;
    }

    /**
     * Registers all resources
     */
    private function registerResources()
    {
        $laradium = app(\Laradium\Laradium\Base\Laradium::class);

        foreach ($laradium->resources() as $resource) {
            $laradium->register($resource);
        }

        foreach ($laradium->apiResources() as $apiResource) {
            $laradium->registerApi($apiResource);
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
                'mail.host'         => setting()->get('mail.mail_host', ''),
                'mail.port'         => setting()->get('mail.mail_port', '2525'),
                'mail.username'     => setting()->get('mail.mail_user', ''),
                'mail.password'     => setting()->get('mail.mail_password', ''),
                'mail.from.address' => setting()->get('mail.mail_from_address', ''),
                'mail.from.name'    => setting()->get('mail.mail_from_name', '')
            ]);
        } catch (\Exception $e) {
        }
    }

    /**
     * Set translatable config
     *
     * @return void
     */
    private function setTranslatableConfig()
    {
        try {
            config([
                'translatable.locales' => translate()->languages()->pluck('iso_code')->toArray()
            ]);
        } catch (\Exception $e) {
        }
    }

    /**
     * @return void
     */
    private function registerBindings()
    {
        \App::bind('Translate', function () {
            return new Translate;
        });
    }

    /**
     * @return void
     */
    private function registerDirectives()
    {
        Blade::directive('lg', function ($expression) {
            return "<?php echo lg($expression); ?>";
        });

        Blade::directive('svg', function ($expression) {
            $file = \File::get(trim($expression, "'"));

            return $file;
        });
    }

    /**
     * @return void
     */
    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../../config/laradium-setting.php' => config_path('laradium-setting.php'),
            __DIR__ . '/../../config/laradium.php'         => config_path('laradium.php'),
            __DIR__ . '/../../config/paperclip.php'        => config_path('paperclip.php'),
            __DIR__ . '/../../config/javascript.php'       => config_path('javascript.php'),
            __DIR__ . '/../../config/translatable.php'     => config_path('translatable.php'),
        ], 'laradium');
    }

    /**
     * @return void
     */
    private function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('laradium', LaradiumMiddleware::class);
    }

    /**
     * @return void
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeLaradiumResource::class,
                MakeLaradiumApiResource::class,
                ImportTranslations::class,
                FindTranslations::class
            ]);
        }
    }

    /**
     * @return void
     */
    private function loadRoutes()
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
    }

    /**
     * @return void
     */
    private function loadViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium');
    }

    /**
     * @return void
     */
    private function loadMigrations()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * @return void
     */
    private function publishAssets()
    {
        $this->publishes([
            __DIR__ . '/../../public/laradium' => public_path('laradium'),
        ], 'laradium');
    }

    /**
     * @return void
     */
    private function registerViewComposers()
    {
        View::composer(
            'laradium::layouts.main', 'Laradium\Laradium\ViewComposers\VariableComposer'
        );
    }
}
