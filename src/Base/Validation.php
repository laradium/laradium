<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class Validation
{

    /**
     * @var ValidationSet
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
     * Validation constructor.
     */
    public function __construct()
    {
        $this->fieldSet = new ValidationSet();
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
     * @return FieldSet
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

    /**
     * @return array
     */
    public function getValidationRules()
    {
        $rules = [];

        return $this->fieldSet()->fields()->mapWithKeys(function ($field) {
            return [
                $field->name() => $field->getRuleSet()
            ];
        })->all();
    }
}