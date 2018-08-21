<?php

namespace Netcore\Aven\Aven\Fields;

use Illuminate\Database\Eloquent\Model;
use Netcore\Aven\Aven\Field;
use Netcore\Aven\Aven\Fields\Hidden;
use Netcore\Aven\Aven\FieldSet;

class MorphsTo extends Field
{

    protected $fieldSet;
    protected $relationName;
    public $morphClass;
    protected $morphModel;
    protected $fields;
    public $morphName;

    /**
     * MorphsTo constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->morphClass = array_first($parameters);
        $this->name = strtolower(array_last(explode('\\', $this->morphClass)));
        $this->morphModel = new $this->morphClass;
        $this->fieldSet = new FieldSet;
        $this->relationName = $this->name;

    }

    public function build($parentAttributeList = [], $model = null)
    {
        $this->parentAttributeList = $parentAttributeList;
        $fields = $this->fieldSet->fields();
        $fieldList = [];


        $attributeList = array_merge($this->parentAttributeList, [
            $this->relationName,
        ]);

        foreach ($fields as $field) {
            $clonedField = clone $field;
            if (!$model) {
                $model = $this->model;
            }
            $morphModel = $this->morphModel->find($model->{$this->morphName . '_id'});
            if ($morphModel) {
                $this->morphModel = $morphModel;
            }

            $clonedField->setModel($this->morphModel);
            $clonedField->build($attributeList, $this->morphModel);


            $fieldList[] = $clonedField;

        }
        $fieldList[] = $this->createContentTypeField($this->morphClass, $attributeList);
        $fieldList[] = $this->createMorphNameField($this->morphClass, $attributeList);

        $this->fields = $fieldList;

        return $this;
    }

    public function formatedResponse($f = null)
    {
        $f = $f ?? $this;

        $items = [];
        foreach ($f->fields as $field) {
            $items[] = $field->formatedResponse();
        }

        return [
            'type'   => 'morph-to',
            'tab'    => $this->tab(),
            'name'   => ucfirst($this->name),
            'fields' => $items
        ];
    }

    public function fields($closure)
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->setModel($this->model());
        $closure($fieldSet);

        return $this;
    }

    public function createContentTypeField($morphClass, $attributeList)
    {
        $field = new Hidden('morph_type', $this->model);
        $field->build($attributeList);
        $field->setValue($morphClass);

        return $field;
    }

    public function createMorphNameField($morphClass, $attributeList)
    {
        $field = new Hidden('morph_name', $this->model);
        $field->build($attributeList);
        $field->setValue($this->morphName);

        return $field;
    }

    public function morphName($value)
    {
        $this->morphName = $value;

        return $this;
    }
}