<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Registries\FieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ApiFieldSet
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
     * @var Collection
     */
    protected $tabs;

    /**
     * FieldSet constructor.
     */
    public function __construct()
    {
        $this->fieldRegistry = app(FieldRegistry::class);
        $this->fields = new Collection;
        $this->tabs = collect(['Main']);
    }

    /**
     * @param $value
     * @return $this
     */
    public function addTab($value)
    {
        $this->tabs->push($value);

        return $this;
    }

    /**
     * @return array
     */
    public function tabs()
    {
        return $this->tabs;
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
     * @param $parameters
     * @return $this
     */
    public function __call($method, $parameters)
    {
        $class = $this->fieldRegistry->getClassByName($method);
        if (class_exists($class)) {
            $field = new $class($parameters, $this->model());
            $this->fields->push($field);

            return $field;
        }

        return $this;
    }
}