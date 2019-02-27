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

if (!function_exists('menuItems')) {
    /**
     * @param $items
     * @return mixed
     */
    function menuItems($items)
    {
        return view('laradium::admin._partials.menu-items', compact('items'))->render();
    }
}

if (!function_exists('getTabId')) {
    /**
     * @param $id
     * @return string
     */
    function getTabId($id)
    {
        return strtolower(str_replace('-', '_', str_replace('\\', '_', $id)));
    }
}

if (!function_exists('is_image')) {
    /**
     * @param $path
     * @return bool
     */
    function is_image($path)
    {
        $extensions = [
            'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'
        ];

        $info = pathinfo($path);

        if (!isset($info['extension'])) {
            return false;
        }

        return in_array($info['extension'], $extensions, true);
    }
}

if (!function_exists('extension_image')) {
    /**
     * @param $path
     * @param string $size
     * @return string
     */
    function extension_image($path, $size = 'md')
    {
        $ignoredExtensions = [
            'jpg', 'jpeg', 'png', 'bmp', 'gif', 'svg'
        ];

        $sizes = [
            'xs' => '16px',
            'sm' => '32px',
            'md' => '48px',
            'lg' => '512px',
        ];

        if (!isset($sizes[$size])) {
            throw \Exception('Size ' . $size . ' doesn\'t exist. Available sizes - xs, sm, md, lg');
        }

        $size = $sizes[$size];
        $info = pathinfo($path);
        $extension = $info['extension'];

        if (in_array($extension, $ignoredExtensions, true)) {
            return asset($path);
        }

        return asset('laradium/admin/assets/images/icons/' . $size . '/' . $extension . '.png');
    }
}
