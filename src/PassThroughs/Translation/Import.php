<?php

namespace Laradium\Laradium\PassThroughs\Translation;

use Laradium\Laradium\PassThroughs\PassThrough;

class Import extends PassThrough
{

    /**
     * @param $rows
     * @return bool
     */
    public function process($rows, $global = false)
    {
        \DB::transaction(function () use ($rows, $global) {
            if ($belongsTo = laradium()->belongsTo()) {
                foreach ($belongsTo->getAll($global) as $item) {
                    $belongsTo->set($item->id);
                    foreach (array_chunk($rows, 300) as $chunk) {
                        foreach (translate()->languages() as $language) {
                            foreach ($chunk as $translation) {
                                if (isset($translation['locale']) && $language->iso_code !== $translation['locale']) {
                                    continue;
                                }

                                \App\Models\Translation::firstOrCreate(
                                    $this->data($translation, [$belongsTo->getForeignKey() => $item->id], 'value'),
                                    $this->data($translation, [$belongsTo->getForeignKey() => $item->id])
                                );
                            }
                        }
                    }
                }
            } else {
                foreach (array_chunk($rows, 300) as $chunk) {
                    foreach ($chunk as $item) {
                        \Laradium\Laradium\Models\Translation::firstOrCreate(
                            $this->data($item, null, 'value'),
                            $this->data($item)
                        );
                    }
                }
            }
        });

        $this->flushCache();

        return true;
    }

    /**
     * @param $item
     * @param null $add
     * @param null $remove
     * @return array
     */
    protected function data($item, $add = null, $remove = null)
    {
        $data = [
            'locale' => $item['locale'],
            'group'  => $item['group'],
            'key'    => $item['key'],
            'value'  => $item['value'],
        ];

        if ($add) {
            $data = array_merge($data, $add);
        }

        if ($remove) {
            unset($data[$remove]);
        }

        return $data;
    }

    /**
     * @return void
     */
    public function flushCache()
    {
        if ($belongsTo = laradium()->belongsTo()) {
            foreach ($belongsTo->getAll($global) as $item) {
                cache()->forget('translations-' . $item->id);
            }
        } else {
            cache()->forget('translations');
        }
    }
}