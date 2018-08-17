<?php

namespace Netcore\Aven\Aven\Fields;

use Netcore\Aven\Aven\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
     * @var string
     */
    protected $view = 'aven::admin.fields.has-many';

    /**
     * @var boolean
     */
    protected $sortable = false;

    /**
     * @var string
     */
    protected $sortableColumn;

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
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function relation(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->model()->load($this->relationName)->{$this->relationName}();
    }

    /**
     * @return array
     */
    public function fieldGroups()
    {
        return $this->fields;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $relation = $this->relation();
        $resource = new $this->resource;
        $resource = $resource->resource()->setModel($this->model)->build();
        $this->resource = $resource;

        foreach ($resource->fieldSet()->fields() as $field) {
            $attributeList = [
                $this->relationName,
                null,
                $field->name()
            ];
            if ($field->isTranslatable()) {
                $count = count($attributeList) - 1;
                $ruleAttributeList = $attributeList;
                $last = $ruleAttributeList[$count];
                unset($ruleAttributeList[$count]);

                foreach (translate()->languages() as $language) {
                    if ($language['is_fallback']) {
                        $this->setValidationRules($this->buildRuleSetKey(array_merge($ruleAttributeList,
                            ['translations', $language['iso_code']], [$last])), $field->getRuleSet());
                    }
                }
            } else {
                $this->setValidationRules($this->buildRuleSetKey($attributeList), $field->getRuleSet());
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
                foreach ($resource->fieldSet()->fields() as $field) {
                    $attributeList = [
                        $this->relationName,
                        null,
                        $field->name()
                    ];
                    $attributeList[1] = $item->id;

                    $f = clone $field;

                    $f->setModel($item);
                    $f->setNameAttributeList($attributeList);
                    $f->setNameAttribute($this->buildNameAttribute($attributeList));
                    $f->setValue($item->getAttribute($f->name()));


                    $fields[$item->id]['fields'][] = $f;
                    if ($this->isSortable()) {
                        $fields[$item->id][$this->sortableColumn] = $item->{$this->sortableColumn};
                    }
                    $fields[$item->id]['id'] = $item->id;
                }
                if ($this->isSortable()) {
                    $fields[$item->id]['fields'][] = $this->createSortableField($item, $attributeList);
                }
                $fields[$item->id]['fields'][] = $this->createIdField($item, $attributeList);

            }

            $this->fields = $fields;
        }

        return $this;
    }

    public function sortable($value)
    {
        $this->sortable = true;
        $this->sortableColumn = $value;

        return $this;
    }

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
        $resource = $this->resource;

        $fields = [];
        foreach ($resource->fieldSet()->fields() as $f) {
            $field = clone $f;

            $attributeList = [
                $this->relationName,
                '__ID__',
                $field->name()
            ];

            $field->setNameAttributeList($attributeList);
            $field->setValue(null);
            $class = get_class($this->model);
            $model = new $class;
            $field->setModel($model);

            $fields[] = $field->formatedResponse($field);
        }

        return [
            'id'     => 0,
            'order'  => 0,
            'fields' => $fields
        ];
    }

    public function formatedResponse($f = null)
    {
        $f ?? $this;
        $items = [];

        foreach ($f->fieldGroups() as $group) {
            $item = [
                'id'    => $group['id'],
                'order' => $this->isSortable() ? $group[$f->sortableColumn] : 0,
            ];
            $url = '';
            foreach ($group['fields'] as $field) {
                $item['fields'][] = $field->formatedResponse();
                $tableName = str_replace('_', '-', $field->model()->getTable());
            }
            $item['url'] = '/admin/' . $tableName . '/' . $group['id'];
            $items[] = $item;
        }

        return [
            'type'        => 'has-many',
            'name'        => $f->relationName,
            'nameLabel'   => ucfirst(str_singular($f->relationName)),
            'is_sortable' => $f->isSortable(),
            'template'    => $f->template(),
            'items'       => $items
        ];
    }

    /**
     * @param $model
     * @param $attributeList
     * @return Hidden
     */
    public function createIdField($model, $attributeList)
    {
        $field = new Hidden(['id'], $model);
        $attributeList[count($attributeList) - 1] = 'id';
        $field->setNameAttribute($this->buildNameAttribute($attributeList));
        $field->setValue($model->getAttribute($field->name()));

        return $field;
    }

    /**
     * @param $model
     * @param $attributeList
     * @return Hidden
     */
    public function createSortableField($model, $attributeList)
    {
        $field = new Hidden([$this->sortableColumn], $model);
        $field->class('js-sortable-item');
        $field->params([
            'orderable' => true
        ]);
        $attributeList[count($attributeList) - 1] = $this->sortableColumn;
        $field->setNameAttribute($this->buildNameAttribute($attributeList));
        $field->setValue($model->getAttribute($field->name()));

        return $field;
    }

}