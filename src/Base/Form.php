<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Fields\HasMany;
use Laradium\Laradium\Base\Fields\MorphsTo;
use Laradium\Laradium\Base\Fields\Tab;
use Laradium\Laradium\Content\Base\Fields\WidgetConstructor;

class Form
{

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @var array
     */
    protected $validationRules = [];

    /**
     * @var bool
     */
    protected $isTranslatable = false;

    /**
     * Form constructor.
     * @param $resource
     */
    public function __construct($resource)
    {
        $this->resource = $resource;
        $this->fields = new Collection;
    }

    /**
     * @return $this
     */
    public function buildForm()
    {
        $resource = $this->resource;
        $fields = $resource->fieldSet()->fields();
        $this->model = $resource->model();

        foreach ($fields as $field) {
            if ($field instanceof Tab) {
                $tabFields = $field->setFieldSet($resource->fieldSet())->build();
                foreach ($tabFields as $tabField) {
                    $tabField->build();
                    $this->setValidationRules($tabField->getRules());

                    $this->fields->push($tabField);

                    if ($tabField->isTranslatable()) {
                        $this->isTranslatable = true;
                    }
                }
            } else {
                $field->build();
                $this->setValidationRules($field->getRules());

                $this->fields->push($field);

                if ($field->isTranslatable()) {
                    $this->isTranslatable = true;
                }
            }
        }

        return $this;
    }

    /**
     * @return array
     */
    public function formatedResponse()
    {
        $fieldList = [];
        foreach ($this->fields as $field) {
            $fieldList[] = $field->formatedResponse($field);
        }

        return $fieldList;
    }

    /**
     * @return Collection
     */
    public function fields(): Collection
    {
        return $this->fields;
    }

    /**
     * @return Model
     */
    public function model(): Model
    {
        return $this->model;
    }

    /**
     * @return string
     */
    public function resourceName(): string
    {
        return str_replace('_', '-', $this->model()->getTable());
    }

    /**
     * @param $rules
     * @return $this
     */
    public function setValidationRules($rules)
    {
        $this->validationRules += $rules;

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
     * @param string $action
     * @return string
     */
    public function getAction($action = 'index'): string
    {
        $resource = $this->resourceName();
        if ($action == 'create') {
            return url('/admin/' . $resource . '/create');
        } else if ($action == 'create') {
            return url('/admin/' . $resource . '/create');
        } else if ($action == 'store') {
            return url('/admin/' . $resource);
        } else if ($action == 'update') {
            return url('/admin/' . $resource . '/' . $this->model->id);
        }

        return url('/admin/' . $resource);
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }
}