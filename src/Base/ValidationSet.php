<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Registries\ValidationFieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ValidationSet
{

    /**
     * @var
     */
    protected $fieldRegistry;

    /**
     * @var Collection
     */
    public $fields;

    /**
     * @var
     */
    protected $model;

    /**
     * FieldSet constructor.
     */
    public function __construct()
    {
        $this->fieldRegistry = app(ValidationFieldRegistry::class);
        $this->fields = new Collection;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @return Collection
     */
    public function fields()
    {
        return $this->fields;
    }

    /**
     * @param $method
     * @return $this
     */
    public function field($parameters)
    {
        $field = new ValidationField($parameters, $this->model());

        $this->fields->push($field);

        return $field;
    }
}