<?php

namespace Laradium\Laradium\Services\Asset;

interface AssetInterface
{
    /**
     * @return AssetInterface
     */
    public function bundle(): self;

    /**
     * @return AssetInterface
     */
    public function vendor(): self;

    /**
     * @return AssetInterface
     */
    public function core(): self;

    /**
     * @return string
     */
    public function render(): string;
}
