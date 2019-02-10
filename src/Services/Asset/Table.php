<?php

namespace Laradium\Laradium\Services\Asset;

use Illuminate\Support\Collection;

class Table
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
     * @return Table
     */
    public function css(): self
    {
        $this->css = true;
        $this->js = false;

        return $this;
    }

    /**
     * @return Table
     */
    public function js(): self
    {
        $this->js = true;
        $this->css = false;

        return $this;
    }

    /**
     * @return string
     */
    public function base(): string
    {
        if ($this->js) {
            return view('laradium::admin.table._partials.base-scripts', [
                'table' => $this
            ])->render();
        }

        return view('laradium::admin.table._partials.base-styles', [
            'table' => $this
        ])->render();
    }

    /**
     * @return string
     */
    public function scripts(): string
    {
        return view('laradium::admin.table._partials.scripts', [
            'table' => $this
        ])->render();
    }

    /**
     * @param \Laradium\Laradium\Base\Table $table
     * @return string
     */
    public function config(\Laradium\Laradium\Base\Table $table): string
    {
        return view('laradium::admin.table._partials.config', [
            'config' => $table->getTableConfig(),
        ])->render();
    }
}