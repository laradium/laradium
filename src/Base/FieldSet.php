<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Registries\FieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FieldSet
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
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
        $this->fieldRegistry = app(FieldRegistry::class);
        $this->fields = new Collection;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function model(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
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
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $class = $this->fieldRegistry->getClassByName($method);
        if (class_exists($class)) {
            $field = new $class($parameters, $this->getModel());
            $this->fields->push($field);

            return $field;
        }

        return $this;
    }
}