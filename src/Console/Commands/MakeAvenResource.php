<?php

namespace Netcore\Aven\Console\Commands;

use File;
use Illuminate\Console\Command;

class MakeAvenResource extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aven:resource {name}';

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
        $namespace = str_replace('\\', '', app()->getNamespace());

        $resourceDirectory = app_path('Aven/Resources');
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating resources directory');
        }
        $dummyResource = File::get(__DIR__.'/../../../stubs/aven-resource.stub');
        $resource = str_replace('{{namespace}}', $namespace, $dummyResource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{resource}}', $name, $resource);
        $resource = str_replace('{{modelNamespace}}', config('aven.default_models_directory', 'App'), $resource);
        $resourceFilePath = app_path('Aven/Resources/' . $name . 'Resource.php');

        if (file_exists($resourceFilePath)) {
            $this->error('Resource already exists!');

            return;
        } else {
            File::put($resourceFilePath, $resource);
            $this->info('Resource successfully created!');
        }

        return;
    }
}
