<?php

namespace Laradium\Laradium\Base\Resources;

use DB;
use Illuminate\Http\Request;
use Laradium\Laradium\Models\Language;
use Laradium\Laradium\Models\Translation;
use Laradium\Laradium\Base\AbstractResource;
use Laradium\Laradium\Base\ColumnSet;
use Laradium\Laradium\Base\FieldSet;

Class TranslationResource extends AbstractResource
{

    /**
     * @var string
     */
    protected $resource = Translation::class;

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource()
    {
        $this->registerEvent('afterSave', function () {
            cache()->forget('translations');
        });

        return laradium()->resource(function (FieldSet $set) {
            $set->select('locale')->options($this->localeList());
            $set->text('group')->rules('required');
            $set->text('key')->rules('required');
            $set->text('value')->rules('required');
        });
    }

    /**
     * @return \Laradium\Laradium\Base\Table
     */
    public function table()
    {
        return laradium()->table(function (ColumnSet $column) {
            $column->add('locale');
            $column->add('group');
            $column->add('key');
            $column->add('value')->editable();
        })->tabs([
            'group' => Translation::select('group')->groupBy('group')->get()->mapWithKeys(function ($translation) {
                return [
                    $translation->group => ucfirst(preg_replace('/[-_]+/', ' ', $translation->group))
                ];
            })->all()
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function import(Request $request)
    {
        $request->validate(['import' => 'required']);

        try {
            $rows = [];
            $excel = app('excel');
            $data = $excel->load($request->file('import'))
                ->get()
                ->toArray();

            foreach ($data as $item) {
                $group = array_first(explode('.', $item['key']));
                $key = str_replace($group . '.', '', $item['key']);

                unset($item['key']);

                $languages = array_keys(array_intersect_key($item, array_flip(array_filter(array_keys($item), 'is_string'))));
                foreach ($languages as $lang) {
                    $rows[] = [
                        'locale' => $lang,
                        'group'  => $group,
                        'key'    => $key,
                        'value'  => $item[$lang],
                    ];
                }
            }

            DB::transaction(function () use ($rows) {
                foreach (array_chunk($rows, 300) as $chunk) {
                    foreach ($chunk as $item) {
                        $translation = Translation::where('key', $item['key'])->where('locale', $item['locale'])->where('group', $item['group'])->first();
                        if ($translation) {
                            $translation->value = $item['value'];
                            $translation->save();
                        } else {
                            Translation::create($item);
                        }
                    }
                }
            });

            cache()->forget('translations');
        } catch (\Exception $e) {
            logger()->error($e);
            return back()->withError('Something went wrong, please try again!');
        }

        return back()->withSuccess('Translations successfully imported!');
    }

    /**
     * @return mixed
     */
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

    /**
     * @return mixed
     */
    protected function localeList()
    {
        return Language::pluck('iso_code', 'iso_code')->toArray();
    }
}