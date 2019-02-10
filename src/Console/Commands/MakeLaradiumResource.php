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
    protected $signature = 'laradium:resource {name} {--m} {--t} {--api} {--shared}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates laradium resource';

    /**
     * @var string
     */
    protected $name;

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
        $this->name = $this->argument('name');

        $resourceDirectory = $this->getResourceDirectory();
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating resources directory');
        }

        $resource = $this->prepareResource();
        $resourceFilePath = $resourceDirectory . '/' . $this->name . 'Resource.php';

//         Create the resource
        if (!file_exists($resourceFilePath)) {
            File::put($resourceFilePath, $resource);
        }

        $this->createApiResource();

        $this->createModel();

        if (!$this->option('shared')) {
            $this->createMenuItem();
        }

        $this->info('Resource successfully created!');

        return;
    }

    /**
     * @return string
     */
    private function getResourceStub(): string
    {
        if ($this->option('shared')) {
            return File::get(__DIR__ . '/../../../stubs/laradium-shared-resource.stub');
        }

        return File::get(__DIR__ . '/../../../stubs/laradium-resource.stub');
    }

    /**
     * @return string
     */
    private function prepareResource(): string
    {
        $namespace = $this->getNamespace();

        $resourceStub = $this->getResourceStub();
        $resource = str_replace('{{namespace}}', $namespace, $resourceStub);
        $resource = str_replace('{{resource}}', $this->name, $resource);
        $resource = str_replace('{{modelNamespace}}', config('laradium.default_models_directory', 'App'), $resource);

        return $resource;
    }

    /**
     * @return string
     */
    private function getResourceDirectory(): string
    {
        if ($this->option('shared')) {
            return app_path('Laradium/Resources/Shared');
        }

        return app_path('Laradium/Resources');
    }

    /**
     * @return string
     */
    private function getNamespace(): string
    {
        $baseNamespace = str_replace('\\', '', app()->getNamespace());
        if ($this->option('shared')) {
            return $baseNamespace . '\Laradium\Resources\Shared';
        }

        return $baseNamespace . '\Laradium\Resources';
    }

    /**
     * @return void
     */
    private function createMenuItem(): void
    {
        $url = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $this->name));
        $menus = [
            'Admin menu' => [
                [
                    'is_active' => 1,
                    'translations' => [
                        'name' => ucfirst(str_replace('-', ' ', $url)),
                        'url' => '/admin/' . str_plural($url),
                    ]
                ]
            ]
        ];

        menu()->seed($menus);
    }

    /**
     * @return void
     */
    private function createApiResource(): void
    {
        if ($this->option('api')) {
            Artisan::call('laradium:api-resource', [
                'name' => $this->name
            ]);
        }
    }


    /**
     * @return void
     */
    private function createModel(): void
    {
        if ($this->option('m')) {
            Artisan::call('make:model', [
                'name' => 'Models/' . $this->name,
                '--migration' => true
            ]);
        }

        if (!$this->option('t')) {
            return;
        }

        Artisan::call('make:model', [
            'name' => 'Models/Translations/' . $this->name . 'Translation',
            '--migration' => true
        ]);
    }
}
