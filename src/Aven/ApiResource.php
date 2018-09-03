<?php

namespace Netcore\Aven\Aven;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiResource
{

    /**
     * @var FieldSet
     */
    protected $fieldSet;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var
     */
    protected $closure;

    /**
     * Resource constructor.
     */
    public function __construct()
    {
        $this->fieldSet = new FieldSet();
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
     * @return Model
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $closure = $this->closure;
        $fieldSet = $this->fieldSet->setModel($this->model());
        $closure($fieldSet);

        return $this;
    }

    /**
     * @return DataSet
     */
    public function fieldSet()
    {
        return $this->fieldSet;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function make(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return $this->closure;
    }
}