<?php

namespace Laradium\Laradium\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;

class MakeLaradiumResource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
<<<<<<< HEAD:src/Console/Commands/MakeLaradiumResource.php
    protected $signature = 'laradium:resource {name} {--t} {--api}';
=======
    protected $signature = 'laradium:resource {name} {--t}';
>>>>>>> master:src/Console/Commands/MakeLaradiumResource.php

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates laradium resource';

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

        $resourceDirectory = app_path('Laradium/Resources');
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating resources directory');
        }

<<<<<<< HEAD:src/Console/Commands/MakeLaradiumResource.php
        // Resource
=======
>>>>>>> master:src/Console/Commands/MakeLaradiumResource.php
        $dummyResource = File::get(__DIR__.'/../../../stubs/laradium-resource.stub');
        $resource = str_replace('{{namespace}}', $namespace, $dummyResource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{modelNamespace}}', config('laradium.default_models_directory', 'App'), $resource);
        $resourceFilePath = app_path('Laradium/Resources/' . $name . 'Resource.php');

        if (!file_exists($resourceFilePath)) {
            File::put($resourceFilePath, $resource);
        }

        // API Resource
        if ($api) {
            $dummyResource = File::get(__DIR__.'/../../../stubs/laradium-api-resource.stub');
            $resource = str_replace('{{namespace}}', $namespace, $dummyResource);
            $resource = str_replace('{{resource}}', $name, $resource);
            $resource = str_replace('{{resource}}', $name, $resource);
            $resource = str_replace('{{modelNamespace}}', config('laradium.default_models_directory', 'App'), $resource);
            $resourceFilePath = app_path('Laradium/Resources/' . $name . 'ApiResource.php');

            if (!file_exists($resourceFilePath)) {
                File::put($resourceFilePath, $resource);
            }
        }

        Artisan::call('make:model', ['name' => 'Models/' . $name]);
        if ($translations) {
            Artisan::call('make:model', ['name' => 'Models/Translations/' . $name . 'Translation']);
        }

        $this->info('Resource successfully created!');

        cache()->forget('laradium::resource-list');

        return;
    }
}
