<?php

namespace Laradium\Laradium\Services\Asset;

class AssetManager
{
    /**
     * @var CssManager
     */
    private $cssManager;

    /**
     * @var JsManager
     */
    private $jsManager;

    /**
     * AssetManager constructor.
     */
    public function __construct()
    {
        $this->cssManager = new CssManager();
        $this->jsManager = new JsManager();
    }

    /**
     * @return CssManager
     */
    public function css(): CssManager
    {
        return $this->cssManager;
    }

    /**
     * @return JsManager
     */
    public function js(): JsManager
    {
        return $this->jsManager;
    }

    /**
     * @return Table
     */
    public function table(): Table
    {
        return new Table();
    }
}
