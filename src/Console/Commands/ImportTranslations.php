<?php

namespace Laradium\Laradium\Console\Commands;

use Illuminate\Console\Command;
use Laradium\Laradium\Models\Translation;

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
        $excelLocation = resource_path('seed_translations/' . $fileName . '.xlsx');
        if (file_exists($excelLocation)) {
            try {
                $rows = [];
                $excel = app('excel');
                $data = $excel->load($excelLocation)
                    ->get()
                    ->toArray();

                foreach ($data as $item) {
                    $group = array_first(explode('.', $item['key']));
                    $key = str_replace($group . '.', '', $item['key']);

                    unset($item['key']);

                    $languages = array_keys($item);
                    foreach ($languages as $lang) {
                        $rows[] = [
                            'locale' => $lang,
                            'group'  => $group,
                            'key'    => $key,
                            'value'  => $item[$lang],
                        ];

                    }
                }
                \DB::transaction(function () use ($rows) {
                    foreach (array_chunk($rows, 300) as $chunk) {
                        foreach ($chunk as $item) {
                            \Laradium\Laradium\Models\Translation::firstOrCreate([
                                'locale' => $item['locale'],
                                'group'  => $item['group'],
                                'key'    => $item['key'],
                            ], [
                                'locale' => $item['locale'],
                                'group'  => $item['group'],
                                'key'    => $item['key'],
                                'value'  => $item['value'],
                            ]);
                        }
                    }

                });
            } catch (\Exception $e) {
                return back()->withError('Something went wrong, please try again!');
            }
        }
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
                            \Laradium\Laradium\Models\Translation::firstOrCreate([
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
