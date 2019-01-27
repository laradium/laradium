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
    protected $signature = 'laradium:resource {name} {--m} {--t} {--api}';

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
        $model = $this->option('m');
        $namespace = str_replace('\\', '', app()->getNamespace());

        $url = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $name));

        $resourceDirectory = app_path('Laradium/Resources');
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating resources directory');
        }

        // Resource
        $dummyResource = File::get(__DIR__ . '/../../../stubs/laradium-resource.stub');
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
            Artisan::call('laradium:api-resource', [
                'name' => $name
            ]);
        }

        if ($model) {
            Artisan::call('make:model', [
                'name'        => 'Models/' . $name,
                '--migration' => true
            ]);

            if ($translations) {
                Artisan::call('make:model', [
                    'name'        => 'Models/Translations/' . $name . 'Translation',
                    '--migration' => true
                ]);
            }
        }

        $menus = [
            'Admin menu' => [
                [
                    'is_active'    => 1,
                    'translations' => [
                        'name' => ucfirst(str_replace('-', ' ', $url)),
                        'url'  => '/admin/' . str_plural($url),
                    ]
                ]
            ]
        ];
        menu()->seed($menus);

        $this->info('Resource successfully created!');

        return;
    }
}
