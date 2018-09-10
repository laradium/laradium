<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Models\Language;

class TranslationRepository
{

    /**
     * @param bool $exceptCurrent
     * @return array|\Illuminate\Contracts\Cache\Repository|mixed
     * @throws \Exception
     */
    public function languages($exceptCurrent = false)
    {
        $languages = cache()->rememberForever('laradium::languages', function () {
            return Language::get()->map(function ($language) {
                if ($language->icon->exists()) {
                    $language->image = $language->icon->url();
                } else {
                    $language->image = null;
                }

                return $language;
            })->toArray();
        });

        if ($exceptCurrent) {
            $languages = array_filter($languages, function ($language) {
                return $language['iso_code'] !== $this->getLanguage()->iso_code;
            });
        }

        return collect($languages)->transform(function ($language) {
            return (object)$language;
        });
    }

    /**
     * @return object
     * @throws \Exception
     */
    public function getLanguage()
    {
        $isoCode = session()->get('locale', config('app.locale'));
        $language = $this->languages()->where('iso_code', $isoCode)->first();
        if ($language) {
            return (object)$language;
        }

        // Fallback to first..
        $language = $this->languages()->first();
        if ($language) {
            return (object)$language;
        }

        // Fallback to iso itself..
        $language = new Language();
        $language->iso_code = $isoCode;

        return (object)$language;
    }
}