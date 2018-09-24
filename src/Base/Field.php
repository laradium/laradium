<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Field
{
    use Translatable;

    /**
     * @var bool
     */
    protected $isHidden = false;

    /**
     * @var
     */
    protected $default;

    /**
     * @var string
     */
    protected $ruleSet;

    /**
     * @var string
     */
    protected $label;

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
    protected $attributeList = [];

    /**
     * @var bool
     */
    protected $isTemplate = false;

    /**
     * @var array
     */
    protected $parentAttributeList = [];

    /**
     * @var string
     */
    protected $tab;

    /**
     * @var array
     */
    protected $col = [
        'size' => 12,
        'type' => 'md'
    ];

    /**
     * @var array
     */
    protected $htmlAttributes = [];

    /**
     * Field constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->name = is_array($parameters) ? array_first($parameters) : $parameters;
        $this->model = $model;
        $this->tab = 'Main';
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        if ($this->isTranslatable() && !$this->isTemplate) {
            $this->value = $this->model()->translateOrNew($this->getLocale())->{$this->name()};
        }

        return $this->value;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setValue($value): self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameAttribute(): string
    {
        $attributeList = $this->getNameAttributeList();

        if ($this->isTranslatable()) {
            if (count($attributeList) === 1) {
                $attributeList = array_merge(['translations', $this->getLocale()], $attributeList);
            } else if (count($attributeList) > 1) {
                $count = count($attributeList) - 1;
                $last = $attributeList[$count];
                unset($attributeList[$count]);

                $attributeList = array_merge($attributeList, ['translations', $this->getLocale()], [$last]);
            }

            $this->nameAttribute = $this->buildNameAttribute($attributeList);

        }

        return $this->nameAttribute;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setNameAttribute($value): self
    {
        $this->nameAttribute = $value;

        return $this;
    }

    /**
     * @param $ruleSet
     * @return $this
     */
    public function rules($ruleSet): self
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
    public function setModel(Model $model): self
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
    public function setNameAttributeList(array $list): self
    {
        $this->attributeList = $list;
        $this->nameAttribute = $this->buildNameAttribute($list);

        return $this;
    }

    /**
     * @return array
     */
    public function getNameAttributeList(): array
    {
        return $this->attributeList;
    }

    /**
     * @param array $parentAttributeList
     * @param null $model
     * @return $this
     */
    public function build($parentAttributeList = [], $model = null): self
    {
        $this->parentAttributeList = $parentAttributeList;
        if ($model) {
            $this->setModel($model);
        }

        $attributeList = array_merge($parentAttributeList, [$this->name()]);

        $this->setNameAttributeList($attributeList);
        $this->setNameAttribute($this->buildNameAttribute($attributeList));

        $this->setValue($this->model->getAttribute($this->name()));

        if ($this->isTranslatable()) {
            $language = translate()->languages()->where('is_fallback', 1)->first();
            if (!$language) {
                $language = translate()->languages()->first();
            }

            if (is_array($attributeList) && count($attributeList) >= 1) {
                $attributeList = $this->arrayInsert($attributeList, count($attributeList) - 1, [$language->iso_code, 'translations']);
            }
        }

        $this->setValidationRules($this->buildRuleSetKey($attributeList), $this->getRuleSet());

        return $this;
    }

    /**
     * @param null $field
     * @return array
     */
    public function formattedResponse($field = null): array
    {
        $field = !is_null($field) ? $field : $this;
        $attributes = collect($field->getNameAttributeList())->map(function ($item, $index) {
            if ($item === '__ID__') {
                return '__ID' . ($index + 1) . '__';
            } else {
                return $item;
            }
        });

        $field->setNameAttributeList($attributes->toArray());

        $attributes = $attributes->filter(function ($item) {
            return str_contains($item, '__ID');
        });

        if (!$field->isTranslatable()) {
            $data = [
                'type'                  => strtolower(array_last(explode('\\', get_class($field)))),
                'name'                  => $field->getNameAttribute(),
                'label'                 => $field->getLabel(),
                'value'                 => $field->getValue(),
                'isTranslatable'        => $field->isTranslatable(),
                'replacementAttributes' => $attributes->toArray(),
                'tab'                   => $this->tab(),
                'col'                   => $this->col,
                'attr'                  => $this->getAttr()
            ];
        } else {

            $data = [
                'type'                  => strtolower(array_last(explode('\\', get_class($field)))),
                'label'                 => $field->getLabel(),
                'isTranslatable'        => $field->isTranslatable(),
                'replacementAttributes' => $attributes->toArray(),
                'tab'                   => $this->tab(),
                'col'                   => $this->col,
                'attr'                  => $this->getAttr()
            ];

            $translatedAttributes = [];

            foreach (translate()->languages() as $language) {
                $field->setLocale($language->iso_code);
                $translatedAttributes[] = [
                    'iso_code' => $language->iso_code,
                    'value'    => $field->getValue(),
                    'name'     => $field->getNameAttribute(),
                ];
            }

            $data['translatedAttributes'] = $translatedAttributes;
        }

        return $data;
    }

    /**
     * @param $value
     * @return $this
     */
    public function isTemplate($value): self
    {
        $this->isTemplate = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->name()));
    }

    /**
     * @param $value
     * @return $this
     */
    public function label($value): self
    {
        $this->label = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function hideIf($value): self
    {
        $this->isHidden = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->isHidden;
    }

    /**
     * @param $value
     * @return $this
     */
    public function default($value): self
    {
        $this->default = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param $attributes
     * @return string
     */
    public function buildNameAttribute($attributes): string
    {
        return implode('', collect($attributes)->filter(function ($item) {
            return $item !== null;
        })->map(function ($item, $index) {
            if ($index !== 0) {
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
        return implode('.', collect($attributes)->map(function ($item, $index) use ($attributes) {
            if ($item === null) {
                $item = '*';
            }

            // TODO: Find better solutions for this
            if (isset($attributes[$index - 1]) && $attributes[$index - 1] === 'blocks' && is_numeric($item)) {
                $item = $item;
            } else if (is_numeric($item)) {
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
    public function setValidationRules($key, $rules): self
    {
        $this->validationRules += [$key => $rules];

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function setTab($value): self
    {
        $this->tab = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function tab(): string
    {
        return $this->tab;
    }

    /**
     * @param $size
     * @param string $type
     * @return $this
     */
    public function col($size = 12, $type = 'md'): self
    {
        $this->col = compact('size', 'type');

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function attr($value): self
    {
        $this->htmlAttributes = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttr(): array
    {
        return $this->htmlAttributes;
    }

    /**
     * @param $array
     * @param $index
     * @param $value
     * @return array
     */
    private function arrayInsert(&$array, $index, $value): array
    {
        $size = count($array);
        if (!is_int($index) || $index < 0 || $index > $size) {
            return $array;
        }

        if (count($value) === 2) {
            foreach ($value as $val) {
                if ($size === 1) {
                    $array = array_prepend($array, $val);
                } else {
                    $temp = array_slice($array, 0, $index);
                    $temp[] = $val;

                    $array = array_merge($temp, array_slice($array, $index, $size));
                }
            }
        } else {
            $temp = array_slice($array, 0, $index);
            $temp[] = $value;

            $array = array_merge($temp, array_slice($array, $index, $size));
        }

        return $array;
    }
}