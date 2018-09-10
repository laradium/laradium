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
        $createTranslations = config('laradium.create_translations_on_the_fly', false);
        if ($createTranslations) {
//            $translations = cache('translations');
//            $languages = languages();
//            if (isset($locale) && !$languages->where('iso_code', $locale)->count()) {
//                $value = $locale;
//            }
//            if (!$locale && !$value && !is_array($replace)) {
//                $value = $replace;
//            }
//            $fallbackLanguage = $languages->where('is_fallback', 1)->first();
//            if($fallbackLanguage) {
//                $fallbackIsoCode = $fallbackLanguage->iso_code;
//                $transKey = $fallbackIsoCode . '.' . $key;
//                if (!isset($translations[$transKey])) {
//                    $translations = [
//                        'key' => $key,
//                    ];
//                    foreach ($languages->pluck('iso_code')->toArray() as $code) {
//                        $translations[$code] = $value;
//                    }
//                    $translation = new Translation();
//                    $translation->import()->process([$translations], false);
//                    cache()->forget('translations');
//                    if (!is_array($replace)) {
//                        $replace = [];
//                    }
//                    $translation = $value;
//                    foreach ($replace as $key => $value) {
//                        $translation = str_replace(':' . $key, $value, $translation);
//                    }
//                    return $translation;
//                }
//            }
        }
        if (!is_array($replace)) {
            $replace = [];
        }
        return trans($key, $replace, $locale);
    }
}