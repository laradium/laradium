<?php

namespace Laradium\Laradium\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\UploadedFile;
use Laradium\Laradium\Imports\TranslationImport;

class ImportTranslations extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @throws \Exception
     */
    public function handle()
    {
        \DB::table('translations')->delete();

        $this->importFromLangFiles();

        $fileName = config('laradium.translations_file');
        $source = resource_path('seed_translations/' . $fileName . '.xlsx');
        if (file_exists($source)) {
            try {
                $copy = str_replace('.xlsx', '_copy.xlsx', $source);
                copy($source, $copy);
                $file = new \Symfony\Component\HttpFoundation\File\File($copy);
                $file = new UploadedFile($file, $file->getBasename(), $file->getMimeType(), null, null, true);

                (new TranslationImport)->import($file);
            } catch (\Exception $e) {
                logger()->error($e);

                $this->error('Something went wrong.');
                exit;
            }
        }
    }

    /**
     *
     */
    private function importFromLangFiles(): void
    {
        $file = resource_path('lang');
        $files = \File::allFiles($file);
        if (file_exists($file)) {
            foreach ($files as $file) {
                $fullPath = $file->getPathname();
                $group = str_replace('.php', '', $file->getFilename());
                foreach (\File::getRequire($fullPath) as $key => $translation) {
                    if (!is_array($key) && !is_array($group) && !is_array($translation)) {
                        foreach (translate()->languages() as $language) {
                            \Laradium\Laradium\Models\Translation::firstOrCreate([
                                'locale' => $language->iso_code,
                                'group'  => $group,
                                'key'    => $key,
                                'value'  => $translation,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
