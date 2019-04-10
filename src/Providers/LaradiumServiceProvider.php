<?php

namespace Laradium\Laradium\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Laradium\Laradium\Console\Commands\FindTranslations;
use Laradium\Laradium\Console\Commands\ImportTranslations;
use Laradium\Laradium\Console\Commands\MakeLaradiumApiResource;
use Laradium\Laradium\Console\Commands\MakeLaradiumResource;
use Laradium\Laradium\Helpers\Translate;
use Laradium\Laradium\Http\Middleware\LaradiumMiddleware;
use Laradium\Laradium\Registries\FieldRegistry;
use Laradium\Laradium\Registries\RouteRegistry;
use Laradium\Laradium\ViewComposers\MenuComposer;
use Laradium\Laradium\ViewComposers\ResourceComposer;
use Laradium\Laradium\ViewComposers\VariableComposer;

class LaradiumServiceProvider extends ServiceProvider
{

    /**
     * @return void
     */
    public function boot(): void
    {

        // Global helpers, icons
        require_once __DIR__ . '/../Helpers/Global.php';
        require_once __DIR__ . '/../Helpers/Icons.php';

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

        // Mail config
        $this->setMailConfig();
        $this->setTranslatableConfig();
        $this->setAdminGuard();
    }

    /**
     * @return void
     */
    private function registerProviders(): void
    {
        $this->app->register(\Dimsav\Translatable\TranslatableServiceProvider::class);
        $this->app->register(\Baum\Providers\BaumServiceProvider::class);
        $this->app->register(LaradiumTranslationServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->app->bind(RouteRegistry::class, function ($app) {
            return new RouteRegistry($app->make(\Illuminate\Routing\Router::class));
        });

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
    private function registerPaperClipConfig(): void
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
    private function getFieldList(): array
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
    private function registerResources(): void
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
    private function setMailConfig(): void
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
    private function setTranslatableConfig(): void
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
    private function registerBindings(): void
    {
        \App::bind('Translate', function () {
            return new Translate;
        });
    }

    /**
     * @return void
     */
    private function registerDirectives(): void
    {
        Blade::directive('lg', function ($expression) {
            return "<?php echo lg($expression); ?>";
        });

        Blade::directive('svg', function ($expression) {
            return '<?php echo (file_exists(' . $expression . ') ? \File::get(' . $expression . ') : ""); ?>';
        });
    }

    /**
     * @return void
     */
    private function publishConfig(): void
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
    private function registerMiddleware(): void
    {
        $this->app['router']->aliasMiddleware('laradium', LaradiumMiddleware::class);
    }

    /**
     * @return void
     */
    private function registerCommands(): void
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
    private function loadRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../../routes/admin.php');
    }

    /**
     * @return void
     */
    private function loadViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'laradium');
    }

    /**
     * @return void
     */
    private function loadMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    /**
     * @return void
     */
    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../../public/laradium' => public_path('laradium'),
            __DIR__ . '/../../public/images'   => public_path('images'),
        ], 'laradium');

        $this->publishes([
            __DIR__ . '/../../public/laradium' => public_path('laradium'),
        ], 'laradium-assets');
    }

    /**
     * @return void
     */
    private function registerViewComposers(): void
    {
        $composers = [
            [
                'views'    => [
                    'laradium::layouts.main',
                ],
                'composer' => ResourceComposer::class
            ],
            [
                'views'    => ['laradium::layouts.main'],
                'composer' => VariableComposer::class
            ],
            [
                'views'    => ['laradium::admin._partials.menu'],
                'composer' => MenuComposer::class
            ],
        ];

        foreach ($composers as $composer) {
            view()->composer($composer['views'], $composer['composer']);
        }
    }

    /**
     * Set translatable config
     *
     * @return void
     */
    private function setAdminGuard(): void
    {
        try {
            config([
                'auth.guards' => array_merge(config('auth.guards'), [
                    'admin' => [
                        'driver'   => 'session',
                        'provider' => 'users',
                    ],
                ])
            ]);
        } catch (\Exception $e) {
        }
    }
}
