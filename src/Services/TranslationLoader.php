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
            $belongsTo = laradium()->belongsTo();
            $cacheKey = $belongsTo ? 'translations-' . ($belongsTo->getCurrent() ?? 'null') : 'translations';
            $this->translations = cache()->rememberForever($cacheKey, function () {
                $translations = [];
                foreach ((class_exists(\App\Models\Translation::class) ? \App\Models\Translation::class : Translation::class)::get() as $item) {
                    $translations[$item->locale][$item->group][$item->key] = $item->value;
                }

                return $translations;
            });
        }
    }
}