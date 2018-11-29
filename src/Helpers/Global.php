<?php

use Illuminate\Support\Facades\File;

if (!function_exists('laradium')) {
    /**
     * @return mixed
     */
    function laradium()
    {
        return app(\Laradium\Laradium\Repositories\LaradiumRepository::class);
    }
}

if (!function_exists('translate')) {
    /**
     * @return mixed
     */
    function translate()
    {
        return app(\Laradium\Laradium\Repositories\TranslationRepository::class);
    }
}

if (!function_exists('menu')) {
    /**
     * @return mixed
     */
    function menu()
    {
        return app(\Laradium\Laradium\Repositories\MenuRepository::class);
    }
}

if (!function_exists('setting')) {
    /**
     * @return mixed
     */
    function setting()
    {
        return app(\Laradium\Laradium\Repositories\SettingsRepository::class);
    }
}

if (!function_exists('versionedAsset')) {
    /**
     * @param $asset
     * @return string
     */
    function versionedAsset($asset)
    {
        $version = @filemtime(public_path($asset)) ?: time();

        return asset($asset) . '?v=' . $version;
    }
}

if (!function_exists('emptyDirectories')) {

    /**
     * @param $directories
     */
    function emptyDirectories($directories)
    {
        if (is_array($directories)) {
            foreach ($directories as $directory) {
                if (is_dir($directory)) {
                    File::deleteDirectory($directory);
                }
            }
        } else {
            if (is_dir($directories)) {
                File::deleteDirectory($directories);
            }
        }
    }
}

if (!function_exists('lg')) {
    /**
     * @param $key
     * @param array $replace
     * @param null $locale
     * @param $value
     * @return String
     */
    function lg($key, $replace = [], $locale = null, $value = null): String
    {
        if (!is_array($replace)) {
            $replace = [];
        }

        $locale = strlen($locale) !== 2 ? app()->getLocale() : $locale;

        return trans($key, $replace, $locale);
    }
}