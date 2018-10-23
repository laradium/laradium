<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\FieldSet;

class HasMany extends Field
{
    protected $relationName;
    protected $fields;
    protected $sortable = false;

    /**
     * @var string
     */
    protected $sortableColumn;

    /**
     * @var FieldSet
     */
    protected $fieldSet;

    /**
     * HasMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->fieldSet = new FieldSet;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->getModel()->{$this->relationName}();
    }
    
    public function build($attributes = [])
    {
        
        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function sortable($value)
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
     * @return mixed
     */
    public function getRelationName()
    {
        return $this->relationName;
    }

    /**
     * @param $model
     * @param $attributeList
     * @return Hidden
     */
    public function createSortableField($model, $attributeList)
    {
        $field = new Hidden($this->sortableColumn, $model);
        $field->build($attributeList);
        $field->class('js-sortable-item');
        $field->params([
            'orderable' => true
        ]);

        return $field;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure)
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->setModel($this->getModel());
        $closure($fieldSet);

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function actions($value)
    {
        $this->actions = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

}