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
     * @return Collection
     */
    public function fieldGroups(): Collection
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

                foreach (config('translatable.locales') as $locale) {
                    $this->setValidationRules($this->buildRuleSetKey(array_merge($ruleAttributeList,
                        ['translations', $locale], [$last])), $field->getRuleSet());
                }
            } else {
                $this->setValidationRules($this->buildRuleSetKey($attributeList), $field->getRuleSet());
            }
        }


        if ($relation->count()) {
            $fields = [];
            foreach ($relation->get() as $item) {
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
                    $fields[$item->id]['id'] = $item->id;
                }
                $fields[$item->id]['fields'][] = $this->createIdField($item, $attributeList);

            }
            $this->fields->push($fields);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function relationName()
    {
        return $this->relationName;
    }

    /**
     * @return string
     */
    public function template()
    {
        $resource = $this->resource;

        $fields = [];
        foreach ($resource->fieldSet()->fields() as $field) {
            if($field->isTranslatable()) {
                $attributeList = [
                    $this->relationName,
                    '__ID__',
                    'translations',
                    '__LOCALE__',
                    $field->name()
                ];
            } else {
                $attributeList = [
                    $this->relationName,
                    '__ID__',
                    $field->name()
                ];
            }

            $explode = explode('\\', get_class($field));
            $name = array_pop($explode);

            $fields[] = [
                'type'           => strtolower($name),
                'nameAttribute'  => $this->buildNameAttribute($attributeList),
                'name'           => $field->name(),
                'isTranslatable' => $field->isTranslatable(),
            ];
        }

        return json_encode($fields);
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

}