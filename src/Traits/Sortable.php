<?php

namespace Laradium\Laradium\Traits;

trait Sortable
{

    /**
     * @var bool
     */
    private $sortable = false;

    /**
     * @var string
     */
    private $sortableColumn;

    /**
     * @param $value
     * @return $this
     */
    public function sortable($value = 'sequence_no')
    {
        $this->sortable = true;
        $this->sortableColumn = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @return string
     */
    public function getSortableColumn()
    {
        return $this->sortableColumn;
    }
}