<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;

class Tab extends Field
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
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = array_first($name);
        $this->fieldSet = new FieldSet;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        return [
            'name'   => $this->name,
            'slug'   => str_slug($this->name, '_'),
            'type'   => 'tab',
            'fields' => $this->fields,
            'config' => [
                'is_translatable' => $this->isTranslatable,
                'col'             => 'col-md-12',
            ]
        ];
    }

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->model($this->model);
        $closure = $this->closure;
        $closure($fieldSet);
        $fields = [];
        foreach ($fieldSet->fields() as $field) {
            if ($field instanceof self) {
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
    public function fields($closure)
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
    public function model($value)
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