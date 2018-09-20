<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\Fields\Hidden;
use Laradium\Laradium\Base\FieldSet;

/**
 * Class MorphsTo
 * @package Laradium\Laradium\Base\Fields
 */
class MorphsTo extends Field
{

    /**
     * @var FieldSet
     */
    protected $fieldSet;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * @var mixed
     */
    public $morphClass;

    /**
     * @var
     */
    protected $morphModel;

    /**
     * @var
     */
    protected $fields;

    /**
     * @var
     */
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

    /**
     * @param array $parentAttributeList
     * @param null $model
     * @return $this|Field
     */
    public function build($parentAttributeList = [], $model = null)
    {
        $this->parentAttributeList = $parentAttributeList;
        $fields = $this->fieldSet->fields();
        $fieldList = [];
        $rules = [];

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
            $rules += $clonedField->getRules();
        }

        $fieldList[] = $this->createContentTypeField($this->morphClass, $attributeList);
        $fieldList[] = $this->createMorphNameField($this->morphClass, $attributeList);

        if ($rules) {
            $this->validationRules = $rules;
        }

        $this->fields = $fieldList;

        return $this;
    }

    /**
     * @param null $f
     * @return array
     */
    public function formattedResponse($f = null)
    {
        $f = $f ?? $this;

        $items = [];
        foreach ($f->fields as $field) {
            $items[] = $field->formattedResponse();
        }

        return [
            'type'   => 'morph-to',
            'tab'    => $this->tab(),
            'col'    => $this->col,
            'name'   => ucfirst($this->name),
            'fields' => $items
        ];
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
     * @param $morphClass
     * @param $attributeList
     * @return \Laradium\Laradium\Base\Fields\Hidden
     */
    public function createContentTypeField($morphClass, $attributeList)
    {
        $field = new Hidden('morph_type', $this->model);
        $field->build($attributeList);
        $field->setValue($morphClass);

        return $field;
    }

    /**
     * @param $morphClass
     * @param $attributeList
     * @return \Laradium\Laradium\Base\Fields\Hidden
     */
    public function createMorphNameField($morphClass, $attributeList)
    {
        $field = new Hidden('morph_name', $this->model);
        $field->build($attributeList);
        $field->setValue($this->morphName);

        return $field;
    }

    /**
     * @param $value
     * @return $this
     */
    public function morphName($value)
    {
        $this->morphName = $value;

        return $this;
    }
}