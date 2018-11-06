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
     * @var
     */
    protected $abstractResource;

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
    public function build()
    {
        $resource = $this->getResource();
        $fields = $resource->fieldSet()->fields();
        $this->model = $resource->getModel();

        foreach ($fields as $field) {
            if ($field instanceof Tab) {
                $tabFields = $field->setFieldSet($resource->fieldSet())->build();
                foreach ($tabFields as $tabField) {
                    $tabField->build();
                    $this->setValidationRules($tabField->getValidationRules());

                    $this->fields->push($tabField);

                    if ($tabField->isTranslatable()) {
                        $this->isTranslatable = true;
                    }
                }
            } else {
                $field->build();
                $this->setValidationRules($field->getValidationRules());

                $this->fields->push($field);

                if ($field->isTranslatable()) {
                    $this->isTranslatable = true;
                }
            }
        }

        return $this;
    }

    public function data()
    {
        $languages = $this->languages();

        return [
            'state' => 'success',
            'data'  => [
                'is_translatable'  => $this->isTranslatable,
                'languages'        => $languages,
                'form'             => $this->response(),
                'default_language' => array_first($languages)['iso_code']
            ]
        ];
    }

    /**
     * @return mixed
     */
    private function languages()
    {
        return translate()->languages()->map(function ($item) {
            return [
                'name'     => $item->title_localized,
                'iso_code' => $item->iso_code,
                'id'       => $item->id,
            ];
        })->toArray();;
    }

    /**
     * @return array
     */
    public function response()
    {
        $fieldList = [];
        foreach ($this->fields as $field) {
            $fieldList[] = $field->formattedResponse($field);
        }

        return $fieldList;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
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
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    public function getModel()
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
        $this->validationRules = array_merge($this->validationRules, $rules);

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
        $slug = $this->getResource()->getSlug();
        if ($action == 'create') {
            return url('/admin/' . $slug . '/create');
        } elseif ($action == 'edit') {
            return url('/admin/' . $slug . '/' . $this->getModel()->id . '/edit');
        } elseif ($action == 'store') {
            return url('/admin/' . $slug);
        } elseif ($action == 'update') {
            return url('/admin/' . $slug . '/' . $this->model->id);
        }

        return url('/admin/' . $slug);
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }
}