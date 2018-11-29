<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Traits\Translatable;

class Field
{

    use Translatable;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $validationRules = [];

    /**
     * @var string
     */
    private $rules;

    /**
     * @var array
     */
    private $replacementAttributes = [];

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @var string
     */
    private $label;

    /**
     * @var Model
     */
    private $model;

    /**
     * @var
     */
    private $value;

    /**
     * @var array
     */
    protected $col = [
        'size' => 12,
        'type' => 'md'
    ];

    /**
     * @var string
     */
    private $tab = 'Main';

    /**
     * @var array
     */
    private $validationAttributes = [];

    /**
     * @var
     */
    private $type;

    /**
     * Field constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->fieldName = array_first($parameters);
        $this->model = $model;
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function build($attributes = [])
    {
        if ($attributes) {
            $this->attributes = $attributes;
        }
        $currentAttributes = $this->attributes;
        $this->attributes = array_merge($currentAttributes, [$this->getFieldName()]);

        if ($this->getRules()) {
            if ($this->isTranslatable()) {
                $languages = (bool)config('laradium.validate_all_languages', false) ? translate()->languages() : translate()->languages()->where('is_fallback', 1);

                foreach ($languages as $language) {
                    $attributes = array_merge($currentAttributes,
                        ['translations', $language->iso_code, $this->getFieldName()]);
                    $this->validationKey($attributes);

                    $this->validationRules += [$this->getValidationKey() => $this->getRules()];
                }
            } else {
                $this->validationRules = [$this->getValidationKey() => $this->getRules()];
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        return [
            'type'         => $this->getType(),
            'label'        => $this->getLabel(),
            'name'         => !$this->isTranslatable() ? $this->getNameAttribute() : null,
            'value'        => !$this->isTranslatable() ? $this->getValue() : null,
            'translations' => $this->getTranslations(),
            'config'       => [
                'is_translatable' => $this->isTranslatable(),
                'col'             => $this->getCol(),
            ]
        ];
    }

    /**
     * @param $value
     * @return $this
     */
    public function value($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value ?? $this->model->{$this->getFieldName()};
    }

    /**
     * @param array $attributes
     */
    public function validationKey($attributes = [])
    {
        $this->validationAttributes = $attributes;
    }

    /**
     * @return string
     */
    public function getValidationKey()
    {
        $attributes = count($this->validationAttributes) ? $this->validationAttributes : $this->getAttributes();

        return implode('.', collect($attributes)
            ->map(function ($item, $index) {
                if (is_numeric($item) || is_null($item) || str_contains($item, '__ID')) {
                    $item = '*';
                }

                return $item;
            })->toArray());
    }

    /**
     * @param $value
     * @return $this
     */
    public function rules($value)
    {
        $this->rules = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param $value
     * @return $this
     */
    public function validationRules($value)
    {
        $this->validationRules = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    /**
     * @param $value
     * @return $this
     */
    public function label($value)
    {
        $this->label = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label ?: ucfirst(str_replace('_', ' ', $this->getFieldName()));
    }

    /**
     * @param $value
     * @return $this
     */
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param $value
     * @return $this
     */
    public function fieldName($value)
    {
        $this->fieldName = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getNameAttribute()
    {
        return implode('', collect($this->getAttributes())->filter(function ($item) {
            return $item !== null;
        })->map(function ($item, $index) {
            if ($index !== 0) {
                return '[' . $item . ']';
            }

            return $item;
        })->toArray());
    }

    /**
     * @param $value
     * @return $this
     */
    public function attributes($value)
    {
        $this->attributes = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param $value
     * @return $this
     */
    public function replacementAttributes($value)
    {
        $this->replacementAttributes = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addReplacementAttribute()
    {
        $attributes = array_merge($this->replacementAttributes, ['__ID__']);

        $replacementAttributes = [];
        foreach ($attributes as $index => $value) {
            $replacementAttributes[] = '__ID' . ($index + 1) . '__';
        }
        $this->replacementAttributes = $replacementAttributes;

        return $this;
    }

    /**
     * @return array
     */
    public function getReplacementAttributes()
    {
        return $this->replacementAttributes;
    }

    /**
     * @param $size
     * @param string $type
     * @return $this
     */
    public function col($size = 12, $type = 'md')
    {
        $this->col = compact('size', 'type');

        return $this;
    }

    /**
     * @return string
     */
    public function getCol()
    {
        return 'col-' . $this->col['type'] . '-' . $this->col['size'];
    }

    /**
     * @param $value
     * @return $this
     */
    public function tab($value)
    {
        $this->tab = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTab()
    {
        return $this->tab;
    }

    /**
     * @param $value
     * @return $this
     */
    public function type($value)
    {
        $this->type = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type ?: strtolower(array_last(explode('\\', get_class($this))));
    }
}