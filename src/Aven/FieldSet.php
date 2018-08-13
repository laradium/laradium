<?php

namespace Netcore\Aven\Aven;

use Netcore\Aven\Registries\FieldRegistry;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class FieldSet
{

    protected $fieldRegistry;
    protected $fields;
    protected $model;

    public function __construct()
    {
        $this->fieldRegistry = app(FieldRegistry::class);
        $this->fields = new Collection;
    }

    public function setModel(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    public function model()
    {
        return $this->model;
    }

    public function fields()
    {
        return $this->fields;
    }

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