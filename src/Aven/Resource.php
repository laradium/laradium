<?php

namespace Netcore\Aven\Aven;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Resource
{

    protected $fieldSet;
    protected $model;
    protected $closure;

    public function __construct()
    {
        $this->fieldSet = new FieldSet();
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

    public function build()
    {
        $closure = $this->closure;
        $fieldSet = $this->fieldSet->setModel($this->model());
        $closure($fieldSet);

        return $this;
    }

    public function fieldSet()
    {
        return $this->fieldSet;
    }

    public function make(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }
}