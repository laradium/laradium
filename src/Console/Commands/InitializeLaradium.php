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
     */
    public function handle()
    {
        $belongsTo = config('laradium.belongsTo', false);
        // TODO: PermissionRole/Group
        $classes = [Language::class, Translation::class, Menu::class, Setting::class, Page::class/*, User::class*/];
        $bar = $this->output->createProgressBar($belongsTo ? count($classes) + 1 : 1);
        $bar->setFormat('[%bar%] %percent:3s%% %message%');
        $bar->setMessage('Setting up Laradium CMS...');

        // Check if there is belongsTo in config
        if ($belongsTo) {
            $tableName = laradium()->belongsTo()->getTable();
            $foreignKey = laradium()->belongsTo()->getForeignKey();

            foreach ($classes as $class) {
                // Copy model to project
                $this->copyModel($class);

                $table = (new $class)->getTable();
                $name = 'add_' . $foreignKey . '_to_' . $table . '_table';

                if ($this->findMigration($name)) {
                    $this->info($name . ' migration already exists, skipping.');
                    continue;
                }

                Artisan::call('make:migration:schema', [
                    'name'     => $name,
                    '--schema' => $foreignKey . ':unsignedInteger:nullable:foreign',
                    '--model'  => 0
                ]);

                // Update languages migration
                if ($name === 'add_' . $foreignKey . '_to_languages_table') {
                    $migration = $this->findMigration($name);
                    $migration = database_path('migrations/' . $migration['full']);

                    $replace = PHP_EOL . "\t\t\t" . 'foreach (config(\'laradium.languages\', []) as $language) {' . PHP_EOL .
                        "\t\t\t" . '$l = \App\Models\Language::where(\'iso_code\', $language[\'iso_code\'])->first();' . PHP_EOL . // TODO: Namespace
                        "\t\t\t" . '$l->update([\'region_id\' => $language[\'region_id\'] ?? null]);' . PHP_EOL .
                        "\t\t" . '}' . PHP_EOL .
                        "\t" . '}';
                    $this->replaceInFile('', $replace, $migration, true);
                }

                $bar->advance();
            }

        }

        // Disable TranslationProvider
        // TODO: Check if this was already done
        $translationProvider = 'Illuminate\Translation\TranslationServiceProvider::class';
        $this->replaceInFile($translationProvider, '//' . $translationProvider . ',', base_path('config/app.php'));
        $bar->advance();

        $bar->finish();
        $this->info('');
        $this->info('Laradium CMS was successfully set up for you!');

        // TODO: If belongsTo exists, create new stub for resource and when creating new resources use that stub
    }

    /**
     * @param $search
     * @param $replace
     * @param $file
     */
    protected function replaceInFile($search, $replace, $file, $isMigration = false)
    {
        $data = file($file, FILE_IGNORE_NEW_LINES);

        if ($isMigration) {
            $data[18] = $replace; // TODO: probably change this
        } else {
            $data = array_map(function ($data) use ($search, $replace) {
                return stristr($data, $search) ? $replace : $data;
            }, $data);
        }

        file_put_contents($file, implode("\n", $data));
    }

    /**
     * @return array
     */
    protected function migrations()
    {
        $path = database_path('migrations');
        $migrations = [];

        foreach (\File::allFiles($path) as $file) {
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
     * @return bool|mixed
     */
    protected function findMigration($name)
    {
        $migrations = $this->migrations();
        $key = array_search($name, array_column($migrations, 'stripped'));

        if (!$key) {
            return false;
        }

        return $migrations[$key];
    }

    /**
     * @param $model
     * @throws \ReflectionException
     */
    protected function copyModel($model)
    {
        $belongsTo = laradium()->belongsTo();
        $model = (new \ReflectionClass($model));

        // Open model file
        $file = file($model->getFileName(), FILE_IGNORE_NEW_LINES);
        $start = 0;
        $end = 0;

        // Get fillable attributes & add domain/region id
        foreach ($file as $key => $line) {
            if (str_contains($line, '$fillable')) {
                $start = $key + 1;
            }

            if ($start) {
                if (str_contains($line, '];')) {
                    $end = $key - 1;

                    break;
                }
            }
        }

        $fillable = [
            '\'' . $belongsTo->getForeignKey() . '\','
        ];

        for ($i = $start; $i <= $end; $i++) {
            $fillable[] = "\t\t" . trim($file[$i]);
        }

        // Create relation
        $relation = [
            'public function ' . $belongsTo->getRelation() . '()',
            "\t" . '{',
            "\t\t" . 'return $this->belongsTo(' . $belongsTo->getClass() . '::class);',
            "\t" . '}'
        ];

        $stub = File::get(__DIR__ . '/../../../stubs/laradium-model.stub');
        $stub = str_replace('{{namespace}}', 'App\Models', $stub);
        $stub = str_replace('{{model}}', $model->getShortName(), $stub);
        $stub = str_replace('{{extendsModel}}', $model->getName(), $stub);
        $stub = str_replace('{{fillable}}', implode("\n", $fillable), $stub);
        $stub = str_replace('{{relation}}', implode("\n", $relation), $stub);

        file_put_contents(app_path('Models/' . $model->getShortName() . '.php'), $stub);
    }
}
