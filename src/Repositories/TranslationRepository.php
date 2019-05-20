<?php

namespace Laradium\Laradium\Repositories;

use Exception;
use Illuminate\Contracts\Cache\Repository;
use Laradium\Laradium\Models\Language;
use Illuminate\Support\Collection;

class TranslationRepository
{

    /**
     * @var Collection
     */
    private $languages;
    /**
     * @param bool $exceptCurrent
     * @return Collection
     * @throws Exception
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

        $this->languages = collect($languages)->transform(function ($language) {
            return (object)$language;
        });

        return $this->languages;
    }

    /**
     * @return object
     * @throws Exception
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

    /**
     * @return array
     * @throws Exception
     */
    public function languagesForForm(): array
    {
        return $this->languages()->map(function ($item) {
            return [
                'name'     => $item->title_localized,
                'iso_code' => $item->iso_code,
                'id'       => $item->id,
            ];
        })->toArray();
    }
}