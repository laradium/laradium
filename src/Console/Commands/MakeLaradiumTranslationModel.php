<?php

namespace Laradium\Laradium\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;

class MakeLaradiumTranslationModel extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradium:translation 
                            {name : Name of the translation model} 
                            {--r : Create a translation model for a resource} 
                            {--w : Create a translation model for a widget}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Laradium translation model';

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $type;

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
        if (!$this->option('r') && !$this->option('w')) {
            $this->info("Please specify the translation model type by using the --w or --r flag.\nSee php artisan laradium:translation help for more info.");
            return;
        }

        $this->name = $this->argument('name');

        $resourceDirectory = $this->getResourceDirectory();
        if (!file_exists($resourceDirectory)) {
            File::makeDirectory($resourceDirectory, 0755, true);
            $this->info('Creating translations directory');
        }

        $model = $this->prepareModel();
        $modelFilePath = $resourceDirectory . '/' . $this->name . 'Translation.php';

//         Create the resource
        if (!file_exists($modelFilePath)) {
            File::put($modelFilePath, $model);
        }

        $this->createModel();

        $this->info('Translation model successfully created!');

        return;
    }

    /**
     * @return string
     */
    private function prepareModel(): string
    {
        $namespace = $this->getNamespace();

        $resource = str_replace([
            '{{namespace}}',
            '{{model}}'
        ], [
            $namespace,
            $this->name
        ], $this->getResourceStub());

        return $resource;
    }

    /**
     * @return string
     */
    private function getResourceDirectory(): string
    {
        if ($this->option('w')) {
            return app_path('App/Models/Widgets/Translations');
        }

        if ($this->option('r')) {
            return app_path('App/Models/Translations');
        }
    }

    /**
     * @return string
     */
    private function getResourceStub(): string
    {
        return File::get(__DIR__ . '/../../../stubs/laradium-translation.stub');
    }


    /**
     * @return string
     */
    private function getNamespace(): string
    {
        $baseNamespace = str_replace('\\', '', app()->getNamespace());
        if ($this->option('w')) {
            return $baseNamespace . '\Models\Widgets\Translations';
        }

        if ($this->option('r')) {
            return $baseNamespace . '\Models\Translations';
        }
    }

    /**
     * @return void
     */
    private function createModel(): void
    {
        if ($this->option('w')) {
            Artisan::call('make:model', [
                'name' => 'Models/Widgets/Translations/' . $this->name . 'Translation',
            ]);
        }

        if ($this->option('r')) {
            Artisan::call('make:model', [
                'name' => 'Models/Translations/' . $this->name . 'Translation',
            ]);
        }
    }
}
