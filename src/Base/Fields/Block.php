<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\FieldSet;

class Block
{
    /**
     * @var
     */
    private $fieldSet;

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
     * @var
     */
    private $fields;

    /**
     * @var array
     */
    private $validationRules = [];

    /**
     * Tab constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->name = array_first($parameters, null, 'col-md-12');
        $this->model = $model;
        $this->fieldSet = new FieldSet;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->name,
            'slug'   => str_slug($this->name, '_'),
            'type'   => 'block',
            'fields' => $this->fields,
            'config' => [
                'is_translatable' => $this->isTranslatable,
                'col'             => $this->name,
            ]
        ];
    }

    /**
     * @return $this
     */
    public function build(): self
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->model($this->model);
        $closure = $this->closure;
        $closure($fieldSet);
        $fields = [];
        foreach ($fieldSet->fields() as $field) {
            if ($field instanceof Tab) {
                $field->model($this->getModel());
            }

            $field->build();

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
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
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
}