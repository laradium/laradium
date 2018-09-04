<?php

namespace Laradium\Laradium\Helpers;

use Laradium\Laradium\Models\Language;

class Translate
{
    /**
     * @return mixed
     */
    public function languages()
    {
        $languages = cache()->get('languages');
        if (!$languages) {
            $languages = cache()->rememberForever('languages', function () {
                return Language::get()->map(function ($item) {
                    if ($item->icon->exists()) {
                        $item->image = $item->icon->url();
                    } else {
                        $item->image = null;
                    }
                    return $item;
                })->toArray();
            });
        }

        return $languages;
    }
}