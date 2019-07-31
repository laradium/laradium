<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;

/**
 * Class MorphTo
 * @package Laradium\Laradium\Base\Fields
 */
class MorphTo extends Field
{

    /**
     * @var
     */
    private $morphable;

    /**
     * @var string
     */
    private $morphable_name;

    /**
     * @var FieldSet
     */
    private $fieldSet;

    /**
     * @var array
     */
    private $fields;

    /**
     * MorphTo constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->fieldName('morph_to');
        $this->morphable = array_first($parameters);
        if (count($parameters) > 1) {
            $this->morphable_name = array_last($parameters);
        } else {
            $this->morphable_name = strtolower(class_basename($this->morphable));
        }

        $this->fieldSet = new FieldSet;

    }

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        parent::build($attributes);

        $this->fields = $this->getFields();

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        $data = parent::formattedResponse();
        $data['value'] = get_class($this);
        $data['fields'] = $this->fields;
        $data['exists'] = $this->getModel()->exists;
        $data['replacement_ids'] = $this->getReplacementAttributes();

        return $data;
    }

    /**
     * @return array
     */
    private function getFields()
    {
        $fields = [];
        $this->model($this->getModel()->{$this->morphable_name} ?: (new $this->morphable));
        $attributes = $this->getAttributes();
        unset($attributes[count($this->getAttributes()) - 1]);
        $this->attributes(array_merge($attributes, [strtolower(class_basename($this->morphable))]));
        $lastReplacementAttribute = [];

        $fields[] = (new Hidden('crud_worker', $this->getModel()))
            ->build($this->getAttributes())
            ->value(get_class($this))
            ->formattedResponse(); // Add hidden crud worker field

        $fields[] = (new Hidden('morphable_type', $this->getModel()))
            ->build($this->getAttributes())
            ->value($this->morphable)
            ->formattedResponse(); // Add hidden morphable type field

        $fields[] = (new Hidden('morphable_name', $this->getModel()))
            ->build($this->getAttributes())
            ->value($this->morphable_name)
            ->formattedResponse(); // Add hidden morphable type field

        if ($this->getModel()->exists) {
            $fields[] = (new Hidden('id', $this->getModel()))
                ->build(array_merge($this->getAttributes(), [$this->getModel()->id]))
                ->value($this->getModel()->id)
                ->formattedResponse(); // Add hidden morphable id field
        } else {
            $this->addReplacementAttribute();
            $lastReplacementAttribute = [array_last($this->getReplacementAttributes())];
        }

        $validationRules = [];

        foreach ($this->fieldSet->fields() as $temporaryField) {
            $field = clone $temporaryField;

            $field->shared($this->isShared());

            if ($this->getModel()->exists) {
                $field->model($this->getModel())
                    ->build(array_merge($this->getAttributes(), [$this->getModel()->id]));
            } else {
                $field->model($this->getModel())
                    ->replacementAttributes($this->getReplacementAttributes())
                    ->build(array_merge($this->getAttributes(), $lastReplacementAttribute));
            }

            if ($field->getValidationRules()) {
                $validationRules[] = $field->getValidationRules();
            }

            $fields[] = $field->formattedResponse();
        }

        $this->validationRules(array_collapse($validationRules));

        return $fields;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure)
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->model($this->getModel());
        $closure($fieldSet);

        return $this;
    }
}