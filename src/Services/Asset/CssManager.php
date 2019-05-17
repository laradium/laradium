<?php

namespace Laradium\Laradium\Services\Asset;


use Illuminate\Support\Collection;
use Throwable;

class CssManager implements AssetInterface
{
    /**
     * @var Collection
     */
    private $vendor;

    /**
     * @var Collection
     */
    private $core;

    /**
     * @var Collection
     */
    private $beforeCore;

    /**
     * @var Collection
     */
    private $afterCore;

    /**
     * @var Collection
     */
    private $list;

    /**
     * CssManager constructor.
     */
    public function __construct()
    {
        $this->vendor = collect([
            versionedAsset('laradium/assets/css/bundle.css'),
            'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
            '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
        ]);

        $this->core = collect([
            versionedAsset('laradium/assets/css/laradium.css')
        ]);

        $this->beforeCore = collect([]);
        $this->afterCore = collect([]);

        $this->list = collect([]);
    }

    /**
     * @param $paths
     * @return $this
     */
    public function beforeCore($paths): self
    {
        if (is_array($paths)) {
            $this->beforeCore = $this->beforeCore->merge($paths);

            return $this;
        }

        $this->beforeCore->push($paths);

        return $this;
    }

    /**
     * @param $paths
     * @return $this
     */
    public function afterCore($paths): self
    {
        if (is_array($paths)) {
            $this->afterCore = $this->afterCore->merge($paths);

            return $this;
        }

        $this->afterCore->push($paths);

        return $this;
    }

    /**
     * @param $paths
     * @return $this
     */
    public function addVendor($paths): self
    {
        if (is_array($paths)) {
            $this->vendor = $this->vendor->merge($paths);

            return $this;
        }

        $this->vendor->push($paths);

        return $this;
    }

    /**
     * return AssetInterface
     */
    public function custom($paths)
    {
        if (is_array($paths)) {
            $this->list = $this->list->merge($paths);

            return $this;
        }

        $this->list->push($paths);

        return $this;
    }

    /**
     * @return CssManager
     */
    public function vendor(): AssetInterface
    {
        $this->list = collect([])
            ->merge($this->vendor);

        return $this;
    }

    /**
     * @return CssManager
     */
    public function core(): AssetInterface
    {
        $this->list = collect([])
            ->merge($this->beforeCore)
            ->merge($this->core)
            ->merge($this->afterCore);

        return $this;
    }

    /**
     * @return AssetInterface
     */
    public function bundle(): AssetInterface
    {
        $this->list = collect([])
            ->merge($this->vendor)
            ->merge($this->beforeCore)
            ->merge($this->core)
            ->merge($this->afterCore);

        return $this;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laradium::admin._partials.assets.css', [
            'assets' => $this->list->toArray()
        ])->render();
    }
}
