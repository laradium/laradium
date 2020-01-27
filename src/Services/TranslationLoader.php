<?php

namespace Laradium\Laradium\Services;

use Illuminate\Contracts\Translation\Loader;
use Laradium\Laradium\Models\Translation;

class TranslationLoader implements Loader
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
    public function load($locale, $group, $key = null)
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

    /**
     * Add a new namespace to the loader.
     *
     * @param  string  $namespace
     * @param  string  $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        // @TODO implementation
    }

    /**
     * Add a new JSON path to the loader.
     *
     * @param  string  $path
     * @return void
     */
    public function addJsonPath($path)
    {
        // @TODO implementation
    }

    /**
     * Get an array of all the registered namespaces.
     *
     * @return array
     */
    public function namespaces()
    {
        // @TODO implementation
    }
}