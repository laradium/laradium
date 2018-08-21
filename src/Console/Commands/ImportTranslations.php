<?php

namespace Netcore\Aven\Console\Commands;

use Illuminate\Console\Command;
use Netcore\Aven\Models\Translation;

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

//        $fileName = config('netcore.module-translate.translations_file');
//        $excelLocation = resource_path('seed_translations/' . $fileName . '.xlsx');
//        if (file_exists($excelLocation)) {
//            try {
//                $excel = app('excel');
//                $all_data = $excel->load($excelLocation)
//                    ->get()
//                    ->toArray();
//                $this->import($all_data);
//            } catch (\Exception $e) {
//                echo "\n\n Could not import translations from excel file. Perhaps format is wrong. \n\n";
//            }
//        }
        cache()->forget('translations');
    }

    /**
     *
     */
    private function importFromLangFiles()
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
                            \Netcore\Aven\Models\Translation::firstOrCreate([
                                'locale' => $language['iso_code'],
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
