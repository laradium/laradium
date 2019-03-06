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
     * @return string
     * @throws \Throwable
     */
    public function custom(): string
    {
        if ($this->js) {
            return view('laradium::admin._partials.assets.js', [
                'assets' => $this->customJs()
            ])->render();
        }

        return view('laradium::admin._partials.assets.css', [
            'assets' => $this->customCss()
        ])->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function core(): string
    {
        if ($this->js) {
            return view('laradium::admin._partials.assets.js', [
                'assets' => [
                    versionedAsset('laradium/assets/js/laradium.js')
                ]
            ])->render();
        }

        return view('laradium::admin._partials.assets.css', [
            'assets' => [
                versionedAsset('laradium/assets/css/laradium.css')
            ]
        ])->render();
    }

    /**
     * @param array $customAssets
     * @return string
     * @throws \Throwable
     */
    public function bundle(array $customAssets = []): string
    {
        if ($this->js) {
            return view('laradium::admin._partials.assets.js', [
                    'assets' => array_merge([
                        versionedAsset('laradium/assets/js/manifest.js'),
                        versionedAsset('laradium/assets/js/vendor.js'),
                        asset('/laradium/admin/assets/plugins/switchery/switchery.min.js'),
                    ], $customAssets)
                ])->render() . $this->js()->core() . $this->js()->custom();
        }

        return view('laradium::admin._partials.assets.css', [
                'assets' => array_merge([
                    versionedAsset('laradium/assets/css/bundle.css'),
                    'https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
                    '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css'
                ], $customAssets)
            ])->render() . $this->css()->core() . $this->css()->custom();
    }
}
