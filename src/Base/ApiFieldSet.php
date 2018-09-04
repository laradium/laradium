<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;

class ApiFieldSet
{

    /**
     * @var Collection
     */
    public $list;

    /**
     * @var string
     */
    public $field;

    /**
     * ColumnSet constructor.
     */
    public function __construct()
    {
        $this->list = new Collection();
    }

    /**
     * @param $field
     * @param null $name
     * @return $this
     */
    public function add($field, $name = null)
    {
        $this->list->push([
            'name'          => $name ?? $field,
            'relation'      => count(explode('.', $field)) > 1 ? array_first(explode('.', $field)) : '',
            'modify'        => null,
        ]);

        $this->field = $field;

        return $this;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function modify($closure)
    {
        $this->list = $this->list->map(function ($item) use ($closure) {
            if ($this->field === $item['name']) {
                $item['modify'] = $closure;
            }

            return $item;
        });

        return $this;
    }

}