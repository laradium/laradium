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

    /**
     * @var string
     */
    private $method = 'post';

    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $redirectTo;

    /**
     * Form constructor.
     * @param $resource
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->fields = new Collection;
        $this->events = collect([]);
    }

    /**
     * @param \Closure $closure
     */
    public function fields(\Closure $closure): self
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
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $this->build();
        $validationRequest = $this->prepareRequest($request);

        $this->fireEvent('beforeSave', $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);

        $model = $this->saveData($request->all(), $this->getModel());

        $this->fireEvent(['afterSave', 'afterCreate'], $request);

        return response()->json($this->data());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $this->build();
        $validationRequest = $this->prepareRequest($request);
        $this->fireEvent('beforeSave', $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);


        $this->saveData($request->all(), $this->getModel());

        $this->fireEvent('afterSave', $request);

        return response()->json($this->data());
    }

    /**
     * @param string $value
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param $value
     * @return $this
     */
    public function method($value): FormNew
    {
        $this->method = $value;

        return $this;
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
            'success' => true,
            'data'    => [
                'message'          => 'Form successfully updated',
                'languages'        => translate()->languagesForForm(),
                'form'             => $this->getFormattedFieldResponse(),
                'is_translatable'  => $this->isTranslatable(),
                'default_language' => translate()->getLanguage()->iso_code,
                'redirectTo'       => $this->getRedirectTo()
            ]
        ];
    }

    /**
     * @param $value
     * @return $this
     */
    public function redirectTo($value)
    {
        $this->redirectTo = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRedirectTo(): ?string
    {
        return $this->redirectTo;
    }

    /**
     * @return array
     */
    public function getFormattedFieldResponse()
    {
        $fieldList = [];

        foreach ($this->getFields() as $field) {
            $fieldList[] = $field->formattedResponse($field);
        }

        return $fieldList;
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @return Resource
     */
    public function getResource()
    {
        return $this->resource;
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
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }
}