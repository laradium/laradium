<?php

namespace Netcore\Aven\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Netcore\Aven\Models\Language;
use Netcore\Aven\Models\Translation;

class TranslationController extends Controller
{

    /**
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $request->validate(['excel' => 'required',]);

        try {
            $existingLanguages = collect(translate()->languages());
            $excel = app('excel');
            $data = $excel->load($request->file('excel'))
                ->get()
                ->toArray();

            foreach ($data as $item) {
                $group = array_first(explode('.', $item['key']));
                $key = str_replace($group . '.', '', $item['key']);

                unset($item['key']);

                $languages = array_keys($item);
                foreach ($languages as $lang) {
                    Translation::firstOrCreate([
                        'locale' => $lang,
                        'group'  => $group,
                        'key'    => $key,
                    ], [
                        'locale' => $lang,
                        'group'  => $group,
                        'key'    => $key,
                        'value'  => $item[$lang],
                    ]);
                }
            }
        } catch (\Exception $e) {
            return back()->withError('Something went wrong, please try again!');
        }

        return back()->withSuccess('Translations successfully imported!');
    }

    public function export()
    {
        $excel = app('excel');
        $filename = 'Translations';
        $title = 'Translations';
        $allTranslations = Translation::orderBy('group', 'asc')
            ->orderBy('key', 'asc')
            ->get();
        $translations = [];
        foreach ($allTranslations as $translation) {
            $translations[$translation->group . '.' . $translation->key][$translation->locale] = $translation;
            $translations[$translation->group . '.' . $translation->key]['group'] = $translation->group;
            $translations[$translation->group . '.' . $translation->key]['key'] = $translation->key;
            $translations[$translation->group . '.' . $translation->key]['id'] = $translation->id;
        }
        $translations = collect($translations)->map(function ($item) {
            return (object)$item;
        })->sortBy('group');
        return $excel->create($filename, function ($excel) use ($translations, $title) {
            $excel->setTitle($title);
            $excel->sheet('Translations', function ($sheet) use ($translations, $title) {
                $languages = Language::all();
                $rows =
                    [
                        [
                            'key',
                        ],
                    ];
                foreach ($languages as $language) {
                    $rows[0][] = $language->iso_code;
                }
                // Now $rows would be something like ['key', 'lv', 'ru']
                $translations = $translations->map(function ($t, $index) use ($languages) {
                    $item = [
                        $t->group . '.' . $t->key,
                    ];
                    foreach ($languages as $language) {
                        $iso_code = $language->iso_code;
                        $translation = object_get($t, $iso_code, new \stdClass());
                        $value = object_get($translation, 'value', '');
                        $item[] = $value;
                    }
                    return $item;
                })->all();
                $rows = array_merge($rows, ($translations));
                $sheet->fromArray($rows, null, 'A1', false, false);
                $sheet->row(1, function ($row) {
                    $row->setFontWeight('bold');
                });
            });
        })->download('xlsx');
    }
}
