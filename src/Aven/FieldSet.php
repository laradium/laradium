<?php

namespace Netcore\Aven\Aven;

use Netcore\Aven\Registries\FieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FieldSet
{

    /**
     * @var
     */
    protected $fieldRegistry;

    /**
     * @var Collection
     */
    protected $fields;

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