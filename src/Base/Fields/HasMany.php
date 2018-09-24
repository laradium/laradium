<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\FieldSet;

class HasMany extends Field
{

    /**
     * @var mixed
     */
    protected $relationName;

    /**
     * @var mixed
     */
    protected $resource;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @var boolean
     */
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
     * @var string
     */
    protected $morphType;

    /**
     * @var array
     */
    protected $actions = ['create', 'delete'];

    /**
     * HasMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->resource = array_pop($parameters);
        $this->fields = new Collection;
        $this->fieldSet = new FieldSet();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->model()->{$this->relationName}();
    }

    /**
     * @return Collection
     */
    public function fieldGroups()
    {
        return $this->fields;
    }

    /**
     * @param array $parentAttributeList
     * @param null $model
     * @return $this
     */
    public function build($parentAttributeList = [], $model = null)
    {
        $this->parentAttributeList = $parentAttributeList;
        $relation = $this->relation();
        $rules = [];
        if ($model) {
            $this->model = $model;
        }

        foreach ($this->fieldSet->fields() as $field) {
            if ($field instanceof MorphsTo) {
                $relation->where($field->morphName . '_type', $field->morphClass);
            }
        }

        if ($relation->count()) {
            $fields = [];

            if ($this->isSortable()) {
                $itemList = $relation->orderBy($this->sortableColumn)->get();
            } else {
                $itemList = $relation->get();
            }

            foreach ($itemList as $item) {
                foreach ($this->fieldSet->fields() as $field) {
                    $attributeList = array_merge($this->parentAttributeList, [
                        $this->relationName,
                        $item->id,
                    ]);

                    $clonedField = clone $field;
                    $clonedField->setModel($item);
                    $clonedField->build($attributeList, $item);

                    $fields[$item->id]['fields'][] = $clonedField;

                    if ($this->isSortable()) {
                        $fields[$item->id][$this->sortableColumn] = $item->{$this->sortableColumn};
                    }

                    $fields[$item->id]['id'] = $item->id;
                    $rules += $clonedField->getRules();
                }

                if ($this->isSortable()) {
                    $fields[$item->id]['fields'][] = $this->createSortableField($item, $attributeList);
                }

                $fields[$item->id]['fields'][] = $this->createIdField($item, $attributeList);
            }

            if ($rules) {
                $this->validationRules = $rules;
            }

            $this->fields = $fields;
        }

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setMorphType($value)
    {
        $this->morphType = $value;

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
    public function relationName()
    {
        return $this->relationName;
    }

    /**
     * @return array
     */
    public function template()
    {
        $hasManyFields = $this->fieldSet->fields();

        $fields = [];
        $model = $this->model->{$this->relationName}()->getModel();
        foreach ($hasManyFields as $f) {
            $field = clone $f;
            $attributeList = array_merge($this->parentAttributeList, [
                $this->relationName,
                '__ID__',
            ]);

            $field->setModel($model);
            $field->build($attributeList, $model);
            $field->isTemplate(true);
            $field->setValue(null);

            $fields[] = $field->formattedResponse($field);
        }

        return [
            'id'     => 0,
            'order'  => 0,
            'fields' => $fields,
            'show'   => false
        ];
    }

    /**
     * @param null $f
     * @return array
     */
    public function formattedResponse($f = null)
    {
        $f = !is_null($f) ? $f : $this;
        $items = [];

        foreach ($f->fieldGroups() as $group) {
            $item = [
                'id'       => $group['id'],
                'url'      => '/admin/resource/' . $group['id'],
                'resource' => get_class($this->relation()->getModel())
            ];

            if ($this->isSortable()) {
                $item['order'] = $group[$this->sortableColumn];
            }

            foreach ($group['fields'] as $field) {
                $item['fields'][] = $field->formattedResponse();
            }

            $items[] = $item;
        }

        return [
            'type'        => 'has-many',
            'full_column' => true,
            'name'        => $f->relationName,
            'label'       => ucfirst(str_singular($f->relationName)),
            'is_sortable' => $f->isSortable(),
            'template'    => $f->template(),
            'tab'         => $this->tab(),
            'col'         => $this->col,
            'items'       => $items,
            'show'        => false,
            'actions'     => $f->getActions()
        ];
    }

    /**
     * @param $model
     * @param $attributeList
     * @return Hidden
     */
    public function createIdField($model, $attributeList)
    {
        $field = new Hidden('id', $model);
        $field->build($attributeList);

        return $field;
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
        $fieldSet->setModel($this->model());
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