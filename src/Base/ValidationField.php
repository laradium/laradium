<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class ValidationField
{
    /**
     * @var string
     */
    protected $ruleSet;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $validationRules = [];

    /**
     * Field constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->name = is_array($parameters) ? array_first($parameters) : $parameters;
        $this->model = $model;
    }

    /**
     * @param $ruleSet
     * @return $this
     */
    public function rules($ruleSet)
    {
        $this->ruleSet = $ruleSet;

        return $this;
    }

    /**
     * @return string
     */
    public function getRuleSet(): string
    {
        return $this->ruleSet ?? '';
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->validationRules;
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
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
     * @return $this
     */
    public function build($model = null)
    {
        if ($model) {
            $this->setModel($model);
        }

        $this->setValidationRules($this->buildRuleSetKey([$this->name()]), $this->getRuleSet());

        return $this;
    }

    /**
     * @param $attributes
     * @return string
     */
    public function buildRuleSetKey($attributes): string
    {
        return implode('.', collect($attributes)->map(function ($item, $index) {
            if (is_null($item)) {
                $item = '*';
            }

            return $item;
        })->toArray());
    }

    /**
     * @param $key
     * @param $rules
     * @return $this
     */
    public function setValidationRules($key, $rules)
    {
        $this->validationRules += [$key => $rules];

        return $this;
    }
}