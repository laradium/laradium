<?php

namespace Laradium\Laradium\Exports;

use Laradium\Laradium\Models\Translation;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TranslationExport implements FromArray, WithHeadings
{
    /**
     * @return
     */
    public function array(): array
    {
        $languages = translate()->languages();
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

        return $translations;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        $data = [
            'key',
        ];

        foreach (translate()->languages() as $language) {
            $data[] = $language->iso_code;
        }

        return $data;
    }
}
