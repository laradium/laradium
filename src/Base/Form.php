<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Fields\Tab;

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
        $this->model($resource->getModel());

        foreach ($fields as $field) {
            if ($field instanceof Tab) {
                $field->model($this->getModel());
            }

            $field->build();
            $this->setValidationRules($field->getValidationRules());

            if ($field->isTranslatable()) {
                $this->isTranslatable = true;
            }

            $this->fields->push($field);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function data()
    {
        $languages = $this->languages();

        return [
            'state' => 'success',
            'data'  => [
                'languages'        => $languages,
                'form'             => $this->response(),
                'is_translatable'  => $this->isTranslatable(),
                'default_language' => array_first($languages)['iso_code'],
                'actions'          => [
                    'index' => $this->getAction()
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    private function languages(): array
    {
        return translate()->languages()->map(function ($item) {
            return [
                'name'     => $item->title_localized,
                'iso_code' => $item->iso_code,
                'id'       => $item->id,
            ];
        })->toArray();
    }

    /**
     * @return array
     */
    public function response()
    {
        $fieldList = [];

        foreach ($this->fields as $field) {
            $response = $field->formattedResponse($field);
            $fieldList[] = $response;
            if ($field instanceof Tab && $response['config']['is_translatable']) {
                $this->isTranslatable = true;
            }
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
     * @param $value
     * @return Form
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
    public function resourceName(): string
    {
        return str_replace('_', '-', $this->getModel()->getTable());
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
        if ($action === 'create') {
            return $this->getUrl('create');
        } else if ($action === 'edit') {
            return $this->getUrl($this->getModel()->id . '/edit');
        } else if ($action === 'store') {
            return $this->getUrl();
        } else if ($action === 'update') {
            return $this->getUrl($this->model->id);
        }

        return $this->getUrl();
    }

    /**
     * @param string $value
     * @return string
     */
    private function getUrl($value = ''): string
    {
        if (!$this->abstractResource) {
            return '';
        }

        if ($this->abstractResource->isShared()) {
            return url('/' . $this->getResource()->getSlug() . '/' . $value);
        }

        return url('/admin/' . $this->getResource()->getSlug() . '/' . $value);
    }

    /**
     * @param $value
     * @return Form
     */
    public function abstractResource($value)
    {
        $this->abstractResource = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }
}