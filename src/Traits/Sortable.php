<?php

namespace Laradium\Laradium\Traits;

use Laradium\Laradium\Base\Fields\Hidden;

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

    /**
     * @param $model
     * @return array
     */
    public function sortableField($model)
    {
        return (new Hidden('sequence_no', $model))
            ->build(array_merge($this->getAttributes(), [$model->id]))
            ->formattedResponse();
    }
}