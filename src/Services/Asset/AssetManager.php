<?php

namespace Laradium\Laradium\Services\Asset;

use Illuminate\Support\Collection;

class AssetManager
{

    /**
     * @return string
     */
    public function table()
    {
        return new Table();
    }
}