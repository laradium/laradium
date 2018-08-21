<?php

if (!function_exists('translate')) {
    /**
     * @return mixed
     */
    function translate()
    {
        return app(\Netcore\Aven\Helpers\Translate::class);
    }
}

if (!function_exists('menu')) {
    /**
     * @return mixed
     */
    function menu()
    {
        return app(\Netcore\Aven\Repositories\MenuRepository::class);
    }
}