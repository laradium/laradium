<?php

namespace Laradium\Laradium\Console\Commands;

use Illuminate\Console\Command;
use Laradium\Laradium\Models\Language;
use Laradium\Laradium\Models\Translation;
use File;
use Symfony\Component\Finder\Finder;
use PhpParser\Lexer;
use PhpParser\ParserFactory;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;
use Maatwebsite\Excel\Writers\LaravelExcelWriter;

class FindTranslations extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'translations:find {--import}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find translations in project and write to the excel sheet';

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
        $paths = $this->getPaths();
        $translations = [];

        // Find translations in files
        $finder = new Finder();
        $finder
            ->in($paths)
            ->name('*.php')
            ->notName('FindTranslations.php')
            ->files();

        // Init parser.
        $parser = (new ParserFactory)->create(ParserFactory::ONLY_PHP7, new Lexer());
        foreach ($finder as $file) {
            if (preg_match_all('/lg(\(((?:[^()]*|(?-2))*)\))/', $file->getContents(), $matches)) {
                foreach ($matches[0] as $caller) {
                    $functionInfo = $parser->parse('<?php ' . $caller . ' ?>');
                    $functionInfo = $functionInfo[0];
                    $i = 0;
                    $key = '';
                    $value = '';
                    foreach ($functionInfo->expr->args as $argument) {
                        if ($argument->value->getType() === 'Scalar_String') {
                            if (!$i) {
                                $key = $argument->value->value;
                            } else {
                                $value = preg_replace('!\s+!', ' ', $argument->value->value);
                            }
                        }
                        $i++;
                    }
                    $translations[$key] = $value;
                }
            }
        }

        // Map translations.
        $mappedTranslations = [];
        foreach ($translations as $key => $text) {
            $data = compact('key');
            foreach (Language::all()->unique('iso_code') as $language) {
                $data[$language->iso_code] = $text;
            }
            $mappedTranslations[] = $data;
        }

        // Write translations to the file.
        $this->writeToFile($mappedTranslations);

        // Import to DB.
        if ($this->option('import')) {
            $this->call('translations:import');
        }

        cache()->forget('translations');
    }

    /**
     * Get paths in which to search for translations.
     *
     * @return array
     */
    protected function getPaths(): array
    {
        $paths = [
            base_path('app'),
            base_path('modules'),
            base_path('resources'),
            base_path('routes'),
        ];
        foreach ($paths as $i => $path) {
            if (!File::isDirectory($path)) {
                unset($paths[$i]);
            }
        }
        return $paths;
    }

    /**
     * @param array $translations
     */
    protected function writeToFile(array $translations): void
    {
        $excel = app('excel');
        $filename = config('laradium.translations_file');
        $excel
            ->create($filename, function (LaravelExcelWriter $writer) use ($translations) {
                $writer->setTitle('Translations');
                $writer->sheet('Translations', function (LaravelExcelWorksheet $sheet) use ($translations) {
                    $sheet->fromArray($translations, '', 'A1');
                    $sheet->row(1, function ($row) {
                        $row->setFontWeight('bold');
                    });
                });
            })
            ->store('xlsx', resource_path('seed_translations'));
    }
}
