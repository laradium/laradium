<?php

namespace Laradium\Laradium\Services;

use Laradium\Laradium\Models\Translation;

class TranslationLoader
{

    /**
     * @var array
     */
    protected $translations;

    /**
     * TranslationLoader constructor.
     */
    public function __construct()
    {
        $this->translations = [];
    }

    /**
     * @param $locale
     * @param $group
     * @param null $namespace
     * @return null
     * @throws \Exception
     */
    public function load($locale, $group, $namespace = null)
    {
        $translations = collect($this->cachedTranslations())->where('locale', $locale)->where('group', $group);
        $translationList = [];
        foreach ($translations as $item) {
            $item = (object)$item;

            if (str_contains($item->key, '.')) {
                $explode = (explode('.', $item->key));
                $group = array_first($explode);
                $key = array_last($explode);
                $translationList[$group][$key] = $item->value;
            }
            else {
                $translationList[$item->key] = $item->value;
            }
        }

        return $translationList;
    }

    /**
     * @throws \Exception
     */
    protected function cachedTranslations()
    {
        return cache()->rememberForever('translations', function () {
            return Translation::get()->map(function ($item) {
                return [
                    'locale' => $item->locale,
                    'key'    => $item->key,
                    'group'  => $item->group,
                    'value'  => $item->value,
                ];
            })->toArray();
        });
    }
}