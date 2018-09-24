<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class Validation
{

    /**
     * @var ValidationSet
     */
    protected $validationSet;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var
     */
    protected $closure;

    /**
     * Validation constructor.
     */
    public function __construct()
    {
        $this->validationSet = new ValidationSet();
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
        $validationSet = $this->validationSet->setModel($this->model());
        $closure($validationSet);

        return $this;
    }

    /**
     * @return FieldSet
     */
    public function validationSet()
    {
        return $this->validationSet;
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

    /**
     * @return array
     */
    public function getValidationRules()
    {
        return $this->validationSet()->fields()->mapWithKeys(function ($field) {
            return [
                $field->name() => $field->getRuleSet()
            ];
        })->all();
    }
}