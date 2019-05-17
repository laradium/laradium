<?php

namespace Laradium\Laradium\Services\Asset;


use Illuminate\Support\Collection;
use Throwable;

class JsManager implements AssetInterface
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
            versionedAsset('laradium/assets/js/manifest.js'),
            versionedAsset('laradium/assets/js/vendor.js'),
        ]);

        $this->core = collect([
            versionedAsset('laradium/assets/js/laradium.js')
        ]);

        $this->beforeCore = collect([]);

        $this->afterCore = collect([
            asset('/laradium/admin/assets/plugins/switchery/switchery.min.js')
        ]);

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
        return view('laradium::admin._partials.assets.js', [
            'assets' => $this->list->toArray()
        ])->render();
    }
}
