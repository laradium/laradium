<?php

namespace Laradium\Laradium\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;

class MakeLaradiumApiResource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradium:api-resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates laradium api resource';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');
        $namespace = str_replace('\\', '', app()->getNamespace());

        $resourceDirectory = app_path('Laradium/Resources/Api');
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating API resources directory');
        }

        $dummyApiResource = File::get(__DIR__ . '/../../../stubs/laradium-api-resource.stub');
        $apiResource = str_replace('{{namespace}}', $namespace, $dummyApiResource);
        $apiResource = str_replace('{{resource}}', $name, $apiResource);
        $apiResource = str_replace('{{resource}}', $name, $apiResource);
        $apiResource = str_replace('{{modelNamespace}}', config('laradium.default_models_directory', 'App'), $apiResource);
        $apiResourceFilePath = app_path('Laradium/Resources/Api/' . $name . 'ApiResource.php');

        if (!file_exists($apiResourceFilePath)) {
            File::put($apiResourceFilePath, $apiResource);
        }

        $this->info('API resource successfully created!');

        return;
    }
}
