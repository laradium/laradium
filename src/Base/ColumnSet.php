<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;

class ColumnSet
{

    /**
     * @var Collection
     */
    public $list;

    /**
     * @var string
     */
    public $column;

    /**
     * ColumnSet constructor.
     */
    public function __construct()
    {
        $this->list = new Collection();
    }

    /**
     * @param $column
     * @param null $name
     * @return $this
     */
    public function add($column, $name = null)
    {
        $this->list->push([
            'column'         => $column,
            'column_parsed'  => str_contains($column, '.') ? array_last(explode('.', $column)) : $column,
            'name'           => $name ?? $column,
            'pretty_name'    => ucfirst(str_replace('_', ' ', $name ?? $column)),
            'title'          => null,
            'relation'       => count(explode('.', $column)) > 1 ? array_first(explode('.', $column)) : '',
            'editable'       => false,
            'translatable'   => false,
            'modify'         => null,
            'not_sortable'   => false,
            'not_searchable' => false,
            'switchable'     => false,
            'width'          => $column === 'action' ? '150px' : null
        ]);

        $this->column = $column;

        return $this;
    }

    /**
     * @return $this
     */
    public function editable()
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['editable'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function translatable()
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['translatable'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function notSortable()
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['not_sortable'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function notSearchable()
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['not_searchable'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function modify($closure)
    {
        $this->list = $this->list->map(function ($item) use ($closure) {
            if ($this->column === $item['column'] && !$item['switchable']) {
                $item['modify'] = $closure;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function switchable($disabled = false)
    {
        $this->list = $this->list->map(function ($item) use ($disabled) {
            if ($this->column === $item['column']) {
                $item['modify'] = function ($row) use ($item, $disabled) {
                    return view('laradium::admin.resource._partials.switcher', [
                        'row'      => $row,
                        'column'   => $item['column'],
                        'disabled' => $disabled
                    ])->render();
                };

                $item['switchable'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param $title
     * @return $this
     */
    public function title($title)
    {
        $this->list = $this->list->map(function ($item) use ($title) {
            if ($this->column === $item['column']) {
                $item['title'] = $title;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param $width
     * @return $this
     */
    public function width($width)
    {
        $this->list = $this->list->map(function ($item) use ($width) {
            if ($this->column === $item['column']) {
                $item['width'] = $width;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param string $width
     * @param string $height
     * @return $this
     */
    public function image($width = '75px', $height = '75px')
    {
        $this->list = $this->list->map(function ($item) use ($width, $height) {
            if ($this->column === $item['column'] && !$item['switchable'] && !$item['editable']) {
                $item['modify'] = function ($row) use ($item, $width, $height) {
                    return '<img src="' . $row->{$item['column']}->url() . '" alt="image" style="width: ' . $width . '; height: ' . $height . '">';
                };
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param $column
     * @return bool
     */
    public function has($column): bool
    {
        return (bool)$this->list->where('column', $column)->count();
    }
}