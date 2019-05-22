<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Fields\Tab;

class Element
{
    /**
     * @var
     */
    private $name;

    /**
     * @var
     */
    private $closure;

    /**
     * @var bool
     */
    private $isTranslatable = false;

    /**
     * @var
     */
    private $model;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var array
     */
    private $validationRules = [];

    /**
     * @var string
     */
    private $type;

    /**
     * @var array
     */
    private $attributes = [];

    /**
     * @var array
     */
    private $replacementAttributes = [];

    /**
     * Element constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->name,
            'type'   => str_slug($this->getType()),
            'fields' => $this->fields,
            'config' => [
                'is_translatable' => $this->isTranslatable,
            ],
            'attr'   => $this->getAttributes()
        ];
    }

    /**
     * @param array $attributes
     * @return $this
     */
    public function build($attributes = []): self
    {
        $fieldSet = new FieldSet();
        $fieldSet->model($this->model);
        $closure = $this->closure;

        if ($closure) {
            $closure($fieldSet);
        }

        $fields = [];

        foreach ($fieldSet->fields as $field) {
            if ($field instanceof Tab) {
                $field->model($this->getModel());
            }

            $field = $field->replacementAttributes($this->getReplacementAttributes())->build($attributes);

            if ($field->isTranslatable()) {
                $this->isTranslatable = true;
            }

            $this->validationRules = array_merge($this->validationRules, $field->getValidationRules());

            $fields[] = $field->formattedResponse();
        }

        $this->fields = $fields;

        return $this;
    }


    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure): self
    {
        $this->closure = $closure;

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
    public function model($value): self
    {
        $this->model = $value;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    /**
     * @return bool
     */
    public function getIsTranslatable(): bool
    {
        return $this->isTranslatable;
    }

    /**
     * @param $value
     * @return $this
     */
    public function type($value): self
    {
        $this->type = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type ?: strtolower(array_last(explode('\\', get_class($this))));
    }

    /**
     * @param $value
     * @return $this
     */
    public function attributes($value): self
    {
        $this->attributes = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $value
     * @return $this
     */
    public function replacementAttributes(array $value): self
    {
        $this->replacementAttributes = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function addReplacementAttribute(): self
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
    public function getReplacementAttributes(): array
    {
        return $this->replacementAttributes;
    }
}
