<?php

namespace Netcore\Aven\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;

class MakeAvenResource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aven:resource {name} {--t} {--api}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates aven resource';

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
        $translations = $this->option('t');
        $api = $this->option('api');
        $namespace = str_replace('\\', '', app()->getNamespace());

        $resourceDirectory = app_path('Aven/Resources');
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating resources directory');
        }

        // Resource
        $dummyResource = File::get(__DIR__.'/../../../stubs/aven-resource.stub');
        $resource = str_replace('{{namespace}}', $namespace, $dummyResource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{modelNamespace}}', config('aven.default_models_directory', 'App'), $resource);
        $resourceFilePath = app_path('Aven/Resources/' . $name . 'Resource.php');

        if (!file_exists($resourceFilePath)) {
            File::put($resourceFilePath, $resource);
        }

        // API Resource
        if ($api) {
            $dummyResource = File::get(__DIR__.'/../../../stubs/aven-api-resource.stub');
            $resource = str_replace('{{namespace}}', $namespace, $dummyResource);
            $resource = str_replace('{{resource}}', $name, $resource);
            $resource = str_replace('{{resource}}', $name, $resource);
            $resource = str_replace('{{modelNamespace}}', config('aven.default_models_directory', 'App'), $resource);
            $resourceFilePath = app_path('Aven/Resources/' . $name . 'ApiResource.php');

            if (!file_exists($resourceFilePath)) {
                File::put($resourceFilePath, $resource);
            }
        }

        Artisan::call('make:model', ['name' => 'Models/' . $name]);
        if ($translations) {
            Artisan::call('make:model', ['name' => 'Models/Translations/' . $name . 'Translation']);
        }

        $this->info('Resource successfully created!');

        return;
    }
}
