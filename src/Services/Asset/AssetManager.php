<?php

namespace Laradium\Laradium\Services\Asset;

use Illuminate\Support\Collection;

class AssetManager
{
    /**
     * @return Table
     */
    public function table()
    {
        return new Table();
    }

    public function core()
    {

    }
}