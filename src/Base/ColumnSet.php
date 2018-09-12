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
            'relation'       => count(explode('.', $column)) > 1 ? array_first(explode('.', $column)) : '',
            'editable'       => false,
            'translatable'   => false,
            'modify'         => null,
            'not_sortable'   => false,
            'not_searchable' => false,
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
            if ($this->column == $item['column']) {
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
            if ($this->column == $item['column']) {
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
            if ($this->column == $item['column']) {
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
            if ($this->column == $item['column']) {
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
            if ($this->column == $item['column']) {
                $item['modify'] = $closure;
            }

            return $item;
        });

        return $this;
    }

}