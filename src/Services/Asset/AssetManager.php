<?php

namespace Laradium\Laradium\Services\Asset;

use Illuminate\Support\Collection;

class AssetManager
{

    /**
     * @var bool
     */
    private $js = false;

    /**
     * @var bool
     */
    private $css = false;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $assetsJs;

    /**
     * @var \Illuminate\Support\Collection
     */
    private $assetsCss;

    /**
     * AssetManager constructor.
     */
    public function __construct()
    {
        $this->assetsJs = collect([]);
        $this->assetsCss = collect([]);
    }

    /**
     * @return AssetManager
     */
    public function css(): self
    {
        $this->css = true;
        $this->js = false;

        return $this;
    }

    /**
     * @return AssetManager
     */
    public function js(): self
    {
        $this->js = true;
        $this->css = false;

        return $this;
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return new Table();
    }

    /**
     * @return array
     */
    public function coreJs(): array
    {
        return [
            versionedAsset('laradium/assets/js/laradium.js')
        ];
    }

    /**
     * @return array
     */
    public function coreCss(): array
    {
        return [
            versionedAsset('laradium/assets/css/laradium.css')
        ];
    }

    /**
     * @return array
     */
    public function customJs(): array
    {
        return config('laradium.custom_js', []);
    }

    /**
     * @return array
     */
    public function customCss(): array
    {
        return config('laradium.custom_css', []);
    }

    /**
     * Custom assets
     *
     * @return $this
     */
    public function custom($assets = []): self
    {
        if ($this->js) {
            $this->assetsJs->push(array_merge($assets, $this->customJs()));

            return $this;
        }

        $this->assetsCss->push(array_merge($assets, $this->customCss()));

        return $this;
    }

    /**
     * Core assets
     *
     * @return $this
     */
    public function core(): self
    {
        if ($this->js) {
            $this->assetsJs->push($this->coreJs());

            return $this;
        }

        $this->assetsCss->push($this->coreCss());

        return $this;
    }

    /**
     * Bundle assets
     *
     * @return $this
     */
    public function bundle(): self
    {
        if ($this->js) {
            $this->assetsJs->push(
                array_merge(
                    [
                        versionedAsset('laradium/assets/js/manifest.js'),
                        versionedAsset('laradium/assets/js/vendor.js'),
                    ],
                    $this->coreJs(),
                    [
                        asset('/laradium/admin/assets/plugins/switchery/switchery.min.js')
                    ],
                    $this->customJs()
                ));

            return $this;
        }

        $this->assetsCss->push(
            array_merge(
                [
                    versionedAsset('laradium/assets/css/bundle.css'),
                    'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                    '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
                ],
                $this->customCss(),
                $this->coreCss()
            )
        );

        return $this;
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function render(): string
    {
        if ($this->js) {
            return view('laradium::admin._partials.assets.js', [
                'assets' => $this->assetsJs->collapse()->toArray()
            ])->render();
        }

        return view('laradium::admin._partials.assets.css', [
            'assets' => $this->assetsCss->collapse()->toArray()
        ])->render();
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        if ($this->js) {
            return $this->assetsJs->collapse();
        }

        return $this->assetsCss->collapse();
    }
}
