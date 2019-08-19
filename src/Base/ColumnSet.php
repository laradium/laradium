<?php

namespace Laradium\Laradium\Base;

use Closure;
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
     * @param string $column
     * @return string
     */
    private function getParsedColumn(string $column): string
    {
        if (str_contains($column, '.')) {
            $parseColumn = explode('.', $column);
            unset($parseColumn[count($parseColumn) - 1]);

            return implode('.', $parseColumn);
        }

        return $column;
    }

    /**
     * @param string $column
     * @param null $name
     * @return $this
     */
    public function add(string $column, $name = null): self
    {
        $this->list->push([
            'column'         => $column,
            'column_parsed'  => $this->getParsedColumn($column),
            'name'           => $name ?? $column,
            'pretty_name'    => $this->getPrettyName($column, $name),
            'title'          => null,
            'relation'       => null,
            'editable'       => false,
            'translatable'   => false,
            'modify'         => null,
            'not_sortable'   => false,
            'not_searchable' => false,
            'switchable'     => false,
            'width'          => null,
            'new'            => false,
            'raw'            => false
        ]);

        $this->column = $column;

        return $this;
    }

    /**
     * @param string $column
     * @param string|null $name
     * @return string
     */
    private function getPrettyName(string $column, string $name = null): string
    {
        return ucfirst(str_replace(['_', '.'], ' ', $name ?? $column));
    }

    /**
     * @return $this
     */
    public function new(): self
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['new'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function relation(string $value): self
    {
        $this->list = $this->list->map(function ($item) use ($value) {
            if ($this->column === $item['column']) {
                $item['column'] = $value . '.' . $item['column'];
                $item['new'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function raw(): self
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['raw'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function editable(): self
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['editable'] = true;
                $item['raw'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function translatable(): self
    {
        $this->list = $this->list->map(function ($item) {
            if ($this->column === $item['column']) {
                $item['translatable'] = true;
                $item['raw'] = true;
            }

            return $item;
        });

        return $this;
    }

    /**
     * @return $this
     */
    public function notSortable(): self
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
    public function notSearchable(): self
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
     * @param Closure $closure
     * @return $this
     */
    public function modify(Closure $closure): self
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
    public function switchable($disabled = false): self
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
     * @param string $title
     * @return $this
     */
    public function title(string $title): self
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
     * @param int|string $width
     * @return $this
     */
    public function width($width): self
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
    public function image(string $width = '75px', string $height = '75px'): self
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
    public function has(string $column): bool
    {
        return (bool)$this->list->where('column', $column)->count();
    }
}
