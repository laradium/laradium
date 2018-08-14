<?php

namespace Netcore\Aven\Aven;

use Netcore\Aven\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Field
{

    use Translatable;

    /**
     * @var string
     */
    protected $ruleSet;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $nameAttribute;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var array
     */
    protected $validationRules = [];

    /**
     * @var array
     */
    protected $setNameAttributeList = [];

    /**
     * Field constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->name = array_first($parameters);
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->isTranslatable()) {
            $this->value = $this->model()->translateOrNew($this->getLocale())->{$this->name()};
        }

        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    public function getNameAttribute()
    {
        $attributeList = $this->getNameAttributeList();

        if ($this->isTranslatable()) {
            if (count($attributeList) == 1) {
                $attributeList = array_merge(['translations', $this->getLocale()], $attributeList);
            } elseif (count($attributeList) > 1) {
                $count = count($attributeList) - 1;
                $last = $attributeList[$count];
                unset($attributeList[$count]);

                $attributeList = array_merge($attributeList, ['translations', $this->getLocale()], [$last]);
            }

            $this->nameAttribute = $this->buildNameAttribute($attributeList);
//            $this->setValidationRules('test', 'required');

        }


        return $this->nameAttribute;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setNameAttribute($value)
    {
        $this->nameAttribute = $value;

        return $this;
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
        return $this->ruleSet??'';
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
     * @return string
     */
    public function view(): string
    {
        return $this->view;
    }

    /**
     * @param array $list
     * @return $this
     */
    public function setNameAttributeList(array $list)
    {
        $this->setNameAttributeList = $list;

        return $this;
    }

    /**
     * @return array
     */
    public function getNameAttributeList()
    {
        return $this->setNameAttributeList;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $attributeList = [
            $this->name()
        ];
        $this->setNameAttributeList($attributeList);
        $this->setNameAttribute($this->buildNameAttribute($attributeList));
        $this->setValue($this->model->getAttribute($this->name()));
        if($this->isTranslatable()) {
            $attributeList = array_merge(['translations', $this->getLocale()], $attributeList);

            foreach(config('translatable.locales') as $locale){
                $this->setValidationRules($this->buildRuleSetKey(array_merge(['translations', $locale], $attributeList)), $this->getRuleSet());
            }
        } else {
            $this->setValidationRules($this->buildRuleSetKey($attributeList), $this->getRuleSet());
        }

        return $this;
    }

    /**
     * @param $attributes
     * @return string
     */
    public function buildNameAttribute($attributes): string
    {
        return implode('', collect($attributes)->map(function ($item, $index) {
            if ($index != 0) {
                return '[' . $item . ']';
            }

            return $item;
        })->toArray());
    }

    /**
     * @param $attributes
     * @return string
     */
    public function buildRuleSetKey($attributes): string
    {
        return implode('.', collect($attributes)->map(function ($item, $index) {
            if (is_integer($item) || is_null($item)) {
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