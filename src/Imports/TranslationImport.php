<?php

namespace Laradium\Laradium\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranslationImport implements ToCollection, WithHeadingRow
{
    use Importable;

    /**
     * @param Collection $rows
     * @throws \Exception
     */
    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows as $row) {
            $row = $row->toArray();
            $group = array_first(explode('.', $row['key']));
            $key = str_replace($group . '.', '', $row['key']);

            unset($row['key']);

            $languages = array_keys(array_intersect_key($row,
                array_flip(array_filter(array_keys($row), 'is_string'))));
            foreach ($languages as $lang) {
                $data[] = [
                    'locale' => $lang,
                    'group'  => $group,
                    'key'    => $key,
                    'value'  => $row[$lang],
                ];
            }
        }

        translate()->import($data);
    }
}
