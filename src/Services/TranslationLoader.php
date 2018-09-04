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
     * @param $key
     * @return null
     */
    public function load($locale, $group, $key)
    {
        try {
            $this->cacheTranslations();
        } catch (\Exception $e) {
        }

        return isset($this->translations[$locale][$group][$key]) ? $this->translations[$locale][$group][$key] : null;
    }

    /**
     * @throws \Exception
     */
    protected function cacheTranslations()
    {
        if (!$this->translations) {
            $this->translations = cache()->rememberForever('translations', function () {
                $translations = [];
                foreach (Translation::all() as $item) {
                    $translations[$item->locale][$item->group][$item->key] = $item->value;
                }

                return $translations;
            });
        }
    }
}