<?php

namespace Netcore\Aven\Aven;

use Illuminate\Support\Collection;

class ColumnSet
{

    public $list;
    public $column;

    /**
     * ColumnSet constructor.
     */
    public function __construct()
    {
        $this->list = new Collection();
    }

    public function add($column, $name = null)
    {
        $this->list->push([
            'column'        => $column,
            'column_parsed' => str_contains($column, '.') ? array_last(explode('.', $column)) : $column,
            'name'          => $name ?? $column,
            'relation'      => count(explode('.', $column)) > 1 ? array_first(explode('.', $column)) : '',
            'editable'      => false
        ]);

        $this->column = $column;

        return $this;
    }

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

}