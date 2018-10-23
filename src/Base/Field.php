<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Field
{
    use Translatable;

    protected $attributes = [];
    private $validationRules = [];
    private $fieldName;
    private $label;
    private $model;

    public function __construct($parameters, Model $model)
    {
        $this->fieldName = array_first($parameters);
        $this->model = $model;
    }

    public function build($attributes = [])
    {
        if ($attributes) {
            $this->attributes = $attributes;
        }
        $this->attributes = array_merge($this->attributes, [$this->getFieldName()]);

        return $this;
    }

    public function formattedResponse()
    {
        return [
            'type'           => strtolower(array_last(explode('\\', get_class($this)))),
            'label'          => $this->getLabel(),
            'name'           => $this->getNameAttribute(),
            'value'          => $this->getValue(),
            'isTranslatable' => $this->isTranslatable(),
            'translations'   => $this->getTranslations()
        ];
    }

    public function rules($value)
    {
        $this->validationRules = $value;

        return $this;
    }

    public function getValue()
    {
        return $this->model->{$this->getFieldName()};
    }

    public function getRules(): array
    {
        return $this->validationRules;
    }

    public function getNameAttribute()
    {
        return implode('.', $this->getAttributes());
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function label($value)
    {
        $this->label = $value;

        return $this;
    }

    public function getLabel()
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->getFieldName()));
    }

    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function getFieldName()
    {
        return $this->fieldName;
    }
}