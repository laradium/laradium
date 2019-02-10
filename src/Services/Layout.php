<?php

namespace Laradium\Laradium\Services;

use Laradium\Laradium\Services\Asset\AssetManager;

class Layout
{

    /**
     * @var string
     */
    private $layout;

    /**
     * @var array
     */
    private $defaultViews = [
        'index' => 'laradium::admin.resource.index',
        'create' => 'laradium::admin.resource.create',
        'edit' => 'laradium::admin.resource.edit'
    ];

    /**
     * @var array
     */
    private $views = [];

    /**
     * Layout constructor.
     */
    public function __construct()
    {
        $this->layout = config('laradium.template_layout', 'laradium::layouts.main');
    }

    /**
     * @param $value
     * @return Layout
     */
    public function set($value): self
    {
        $this->layout = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->layout;
    }

    /**
     * @param $name
     * @return string
     */
    public function getView($name): string
    {
        if (!isset($this->views[$name])) {
            return $this->defaultViews[$name];
        }

        return $this->views[$name];
    }

    /**
     * @param $views
     * @return $this
     */
    public function views($views)
    {
        $this->views = $views;

        return $this;
    }

    /**
     * @return AssetManager
     */
    public function assetManager(): AssetManager
    {
        return new AssetManager;
    }
}