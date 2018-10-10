<?php

namespace Laradium\Laradium\Console\Commands;

use Artisan;
use File;
use Illuminate\Console\Command;
use Laradium\Laradium\Content\Models\Page;
use Laradium\Laradium\Models\Language;
use Laradium\Laradium\Models\Menu;
use Laradium\Laradium\Models\Setting;
use Laradium\Laradium\Models\Translation;

class InitializeLaradium extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laradium:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize Laradium CMS';

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
     * @throws \ReflectionException
     */
    public function handle()
    {
        $belongsTo = config('laradium.belongsTo', false);
        $classes = [Language::class, Translation::class, Menu::class, Setting::class, Page::class];

        $bar = $this->output->createProgressBar($belongsTo ? count($classes) + 2 : 1);
        $bar->setFormat('[%bar%] %percent:3s%% %message%');
        $bar->setMessage('Setting up Laradium CMS...');

        $bar->advance();

        // Check if there is belongsTo in config
        if ($belongsTo) {
            $foreignKey = laradium()->belongsTo()->getForeignKey();

            // Add users migration first
            $name = 'add_' . $foreignKey . '_to_users_table';
            if (!$this->findMigration($name)) {
                Artisan::call('make:migration:schema', [
                    'name'     => $name,
                    '--schema' => $foreignKey . ':unsignedInteger:nullable:foreign',
                    '--model'  => 0
                ]);
            }

            $bar->advance();

            $this->info('');

            foreach ($classes as $class) {
                if (!class_exists($class)) {
                    continue;
                }

                // Copy model&resource to project
                $this->copyModel($class);
                $this->copyResource($class);

                $table = (new $class)->getTable();
                $name = 'add_' . $foreignKey . '_to_' . $table . '_table';
                if ($this->findMigration($name)) {
                    $this->info($name . ' migration already exists, skipping.');
                    continue;
                }

                // Create migration
                Artisan::call('make:migration:schema', [
                    'name'     => $name,
                    '--schema' => $foreignKey . ':unsignedInteger:nullable:foreign',
                    '--model'  => 0
                ]);

                // Update languages migration
                if ($name === 'add_' . $foreignKey . '_to_languages_table') {
                    $migration = $this->findMigration($name);
                    $migration = database_path('migrations/' . $migration['full']);

                    $replace = PHP_EOL . "\t\t" . 'foreach (config(\'laradium.languages\', []) as $language) {' . PHP_EOL .
                        "\t\t\t" . '$l = ' . config('laradium.default_models_directory') . '\Language::where(\'iso_code\', $language[\'iso_code\'])->first();' . PHP_EOL .
                        "\t\t\t" . '$l->update([\'' . $foreignKey . '\' => $language[\'' . $foreignKey . '\'] ?? null]);' . PHP_EOL .
                        "\t\t" . '}' . PHP_EOL .
                        "\t" . '}';
                    $this->replaceInFile('', $replace, $migration, true);
                }

                $bar->advance();
            }
        }

        // Disable TranslationProvider
        $translationProvider = 'Illuminate\Translation\TranslationServiceProvider::class';
        $this->replaceInFile($translationProvider, '//' . $translationProvider . ',', base_path('config/app.php'));
        $bar->advance();

        $bar->finish();
        $this->info('');
        $this->info('Laradium CMS was successfully set up for you!');
    }

    /**
     * @param $search
     * @param $replace
     * @param $file
     * @param bool $isMigration
     */
    protected function replaceInFile($search, $replace, $file, $isMigration = false): void
    {
        $data = file($file, FILE_IGNORE_NEW_LINES);

        if ($isMigration) {
            $data[18] = $replace;
        } else {
            $data = array_map(function ($data) use ($search, $replace) {
                return stristr($data, $search) ? $replace : $data;
            }, $data);
        }

        // Write changes to file
        file_put_contents($file, implode("\n", $data));
    }

    /**
     * @return array
     */
    protected function migrations(): array
    {
        $path = database_path('migrations');
        $migrations = [];

        foreach (File::allFiles($path) as $file) {
            $name = str_replace('.php', '', $file->getBaseName());
            $name = explode('_', $name);
            $name = array_diff_key($name, array_flip([0, 1, 2, 3]));
            $name = implode('_', $name);

            $migrations[] = [
                'stripped' => $name,
                'full'     => $file->getBaseName()
            ];
        }

        return $migrations;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function findMigration($name)
    {
        $migrations = $this->migrations();
        $key = array_search($name, array_column($migrations, 'stripped'));

        if ($key === false) {
            return false;
        }

        return $migrations[$key];
    }

    /**
     * @param $model
     * @throws \ReflectionException
     */
    protected function copyModel($model): void
    {
        $belongsTo = laradium()->belongsTo();
        $fillable = array_prepend((new $model)->getFillable(), $belongsTo->getForeignKey());
        $model = new \ReflectionClass($model);

        $fillable = collect($fillable)->map(function ($field, $index) {
            return $index === 0 ? '\'' . $field . '\',' : "\t\t" . '\'' . $field . '\',';
        })->all();

        // Create relation
        $relation = [
            'public function ' . $belongsTo->getRelation() . '()',
            "\t" . '{',
            "\t\t" . 'return $this->belongsTo(' . $belongsTo->getClass() . '::class);',
            "\t" . '}'
        ];

        $stub = File::get(__DIR__ . '/../../../stubs/belongsTo/laradium-model.stub');
        $stub = str_replace([
            '{{namespace}}', '{{model}}', '{{extendsModel}}', '{{fillable}}', '{{relation}}'
        ], [
            config('laradium.default_models_directory'), $model->getShortName(), $model->getName(), implode("\n", $fillable), implode("\n", $relation)
        ], $stub);

        // Write to file
        file_put_contents(app_path('Models/' . $model->getShortName() . '.php'), $stub);
    }

    /**
     * @param $model
     * @throws \ReflectionException
     */
    protected function copyResource($model): void
    {
        $class = new \ReflectionClass($model);
        $resource = $this->getResourceNamespace($class->getShortName());

        $namespace = str_replace('\\', '', app()->getNamespace());
        $stub = File::get(__DIR__ . '/../../../stubs/belongsTo/laradium-resource.stub');
        $stub = str_replace([
            '{{namespace}}', '{{resource}}', '{{extendsResource}}', '{{modelNamespace}}'
        ], [
            $namespace, $class->getShortName(), '\\' . $resource, config('laradium.default_models_directory', 'App')
        ], $stub);

        foreach (['resource', 'table'] as $k => $function) {
            $func = new \ReflectionMethod($resource, $function);

            $f = $func->getFileName();
            $startLine = $func->getStartLine() - 1;
            $endLine = $func->getEndLine();

            $source = file($f, FILE_IGNORE_NEW_LINES);

            // Function content
            $body = [];
            for ($i = $startLine; $i < $endLine; $i++) {
                $body[] = $i === $startLine ? trim($source[$i]) : $source[$i];
            }

            $body = $this->{'write' . ucfirst($function)}($class->getShortName(), $body);
            $stub = str_replace('{{' . $function . 'Method}}', implode("\n", $body), $stub);
        }

        // Write to file
        file_put_contents(app_path('Laradium/Resources/' . $class->getShortName() . 'Resource.php'), $stub);
    }

    /**
     * @param $class
     * @param $body
     * @return array
     */
    protected function writeResource($class, $body): array
    {
        $write = [
            "\t\t\t" . $this->getSelect($class),
        ];

        foreach ($body as $key => $line) {
            if (str_contains($line, 'laradium()->resource(function (FieldSet $set)')) {
                $insert = $key + 1;
            }
        }

        if ($insert) {
            array_splice($body, $insert, 0, implode("\n", $write));
        }

        return $body;
    }

    /**
     * @param $class
     * @param $body
     * @return array
     */
    protected function writeTable($class, $body): array
    {
        $write = [
            '',
            "\t\t" . '$belongsToForeignKey = laradium()->belongsTo()->getForeignKey();',
            "\t\t" . '$belongsToId = auth()->user()->{$belongsToForeignKey};' . PHP_EOL,
            "\t\t" . 'if (!$belongsToId) {',
            "\t\t\t" . '$table->tabs([',
            "\t\t\t\t" . '$belongsToForeignKey => ' . $this->getTabs($class) . '',
            "\t\t\t" . ']);',
            "\t\t" . '}',
        ];

        foreach ($body as $key => $line) {
            if (str_contains($line, ');')) {
                $insert = $key + 1;
            }
        }

        if ($insert) {
            array_splice($body, $insert, 0, implode("\n", $write));
        }

        return $body;
    }

    /**
     * @param $class
     * @return string
     */
    protected function getSelect($class): string
    {
        $classes = [
            'Language'    => 'laradium()->belongsTo()->getSelect($set);',
            'Menu'        => 'laradium()->belongsTo()->getSelect($set, [], true, true);',
            'Translation' => 'laradium()->belongsTo()->getSelect($set, $this->onChange(), false, true);',
            'Page'        => 'laradium()->belongsTo()->getSelect($set, [], true);',
        ];

        return array_get($classes, $class, '');
    }

    /**
     * @param $class
     * @return string
     */
    protected function getTabs($class): string
    {
        $classes = [
            'Language'    => 'laradium()->belongsTo()->getOptions()',
            'Menu'        => 'laradium()->belongsTo()->getOptions(true)',
            'Setting'     => 'laradium()->belongsTo()->getOptions(true)',
            'Translation' => 'laradium()->belongsTo()->getOptions(true)',
            'Page'        => 'laradium()->belongsTo()->getOptions()',
        ];

        return array_get($classes, $class, '');
    }

    /**
     * @param $class
     * @return string
     */
    protected function getResourceNamespace($class): string
    {
        $resources = [
            'Language'    => 'Laradium\Laradium\Base\Resources\\',
            'Menu'        => 'Laradium\Laradium\Base\Resources\\',
            'Setting'     => 'Laradium\Laradium\Base\Resources\\',
            'Translation' => 'Laradium\Laradium\Base\Resources\\',
            'Page'        => 'Laradium\Laradium\Content\Base\Resources\\',
        ];

        return array_get($resources, $class, '') . $class . 'Resource';
    }
}
