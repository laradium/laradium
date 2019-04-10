<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Fields\Tab;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;

class FormNew
{

    private const CODE_RESPONSE_SUCCESSFUL = 200;

    use Crud, CrudEvent;
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
    protected $isTranslatable = true;

    /**
     * @var
     */
    protected $abstractResource;

    /**
     * @var Collection
     */
    private $fieldSetFields;

    private $url;

    private $returnUrl;

    /**
     * Form constructor.
     * @param $resource
     */
    public function __construct()
    {
        $this->fields = new Collection;
        $this->events = collect([]);
    }

    /**
     * @param \Closure $closure
     */
    public function fld(\Closure $closure): self
    {
        $fieldSet = (new FieldSet)->model($this->getModel());
        $closure($fieldSet);

        $this->fieldSetFields = $fieldSet->fields();

        return $this;
    }

    /**
     * @return Collection
     */
    private function getFieldSetFields()
    {
        return $this->fieldSetFields;
    }

    /**
     * @return Model
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
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validationRequest = $this->prepareRequest($request);

        $this->fireEvent('beforeSave', $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);

        $model = $this->saveData($request->all(), $this->getModel());

        $this->fireEvent(['afterSave', 'afterCreate'], $request);

        return response()->json([
            'code' => self::CODE_RESPONSE_SUCCESSFUL,
            'data' => $this->data()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $validationRequest = $this->prepareRequest($request);
        $this->fireEvent('beforeSave', $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);

        $this->saveData($request->all(), $this->getModel());

        $this->fireEvent('afterSave', $request);

        return response()->json([
            'code' => self::CODE_RESPONSE_SUCCESSFUL,
            'data' => $this->data()
        ]);
    }

    /**
     * @param string $action
     * @return string
     */
    public function getAction($action = 'store'): string
    {
        switch ($action) {
            case 'store':
                return $this->getUrl();
                break;
            case 'update':
                return $this->getUrl($this->getModel()->id);
                break;
            default:
                return $this->getUrl();
                break;
        }
    }

    /**
     * @param string $value
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param $value
     * @return $this
     */
    public function url($value): FormNew
    {
        $this->url = $value;

        return $this;
    }


    /**
     * @return $this
     */
    public function build()
    {
        foreach ($this->getFieldSetFields() as $field) {
            if ($field instanceof Tab) {
                $field->model($this->getModel());
            }

            $field->build();
            $this->setValidationRules($field->getValidationRules());

            $this->fields->push($field);

        }

        return $this;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        return view('laradium::admin._partials.form', [
            'form' => $this
        ]);
    }

    /**
     * @return array
     */
    public function data()
    {
        return [
            'state' => 'success',
            'data'  => [
                'languages'        => translate()->languagesForForm(),
                'form'             => $this->response(),
                'is_translatable'  => $this->isTranslatable(),
                'default_language' => translate()->getLanguage()->iso_code,
                'actions'          => [
                    'index' => $this->returnUrl
                ]
            ]
        ];
    }

    public function returnUrl($value)
    {
        $this->returnUrl = $value;

        return $this;
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
     * @param $rules
     * @return $this
     */
    public function setValidationRules($rules)
    {
        $this->validationRules = array_merge($this->getValidationRules(), $rules);

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