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