<?php

namespace Laradium\Laradium\Base;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Laradium\Laradium\Base\Fields\Tab;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;
use ReflectionException;

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
     * @var string|null
     */
    private $returnUrl;

    /**
     * Form constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->fields = new Collection;
        $this->events = collect([]);
    }

    /**
     * @param Closure $closure
     * @return FormNew
     */
    public function fields(Closure $closure): self
    {
        $fieldSet = (new FieldSet)->model($this->getModel());
        $closure($fieldSet);

        $this->fieldSetFields = $fieldSet->fields();

        return $this;
    }

    /**
     * @return Collection
     */
    private function getFieldSetFields(): Collection
    {
        return $this->fieldSetFields;
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param Collection $events
     * @return $this
     */
    public function events(Collection $events): self
    {
        $this->events = $events;

        return $this;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function store(Request $request): JsonResponse
    {
        $this->build();
        $validationRequest = $this->prepareRequest($request);

        $this->fireEvent(['beforeSave', 'beforeCreate'], $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);

        $model = $this->saveData($request->all(), $this->getModel());
        $this->model($model);

        $this->fireEvent(['afterSave', 'afterCreate'], $request);

        return response()->json($this->data());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ReflectionException
     */
    public function update(Request $request): JsonResponse
    {
        $this->build();
        $validationRequest = $this->prepareRequest($request);
        $this->fireEvent(['beforeSave', 'beforeUpdate'], $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);


        $model = $this->saveData($request->all(), $this->getModel());
        $this->model($model);

        $this->fireEvent(['afterSave', 'afterUpdate'], $request);

        return response()->json($this->data());
    }

    /**
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
     * @return string
     */
    public function getUrl(): string
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
    public function build(): self
    {
        foreach ($this->getFieldSetFields() as $field) {
            $field->build();
            $this->setValidationRules($field->getValidationRules());

            $this->fields->push($field);

        }

        return $this;
    }

    /**
     * @return View
     */
    public function render(): View
    {
        return view('laradium::admin._partials.form', [
            'form' => $this
        ]);
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return [
            'success' => true,
            'data'    => [
                'message'          => 'Form successfully updated',
                'languages'        => translate()->languagesForForm(),
                'form'             => $this->getFormattedFieldResponse(),
                'is_translatable'  => $this->isTranslatable(),
                'default_language' => translate()->getLanguage()->iso_code,
                'redirect_to'      => $this->getRedirectTo(),
                'return_to'        => $this->getReturnUrl(),
            ]
        ];
    }

    /**
     * @param Closure $closure
     * @return $this
     */
    public function redirectTo(Closure $closure): self
    {
        $this->redirectTo = $closure;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRedirectTo(): ?string
    {
        if(!$this->redirectTo) {
            return null;
        }
        $closure = $this->redirectTo;

        return $closure($this->getModel());
    }

    /**
     * @param string $value
     * @return $this
     */
    public function returnUrl(string $value): self
    {
        $this->returnUrl = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getReturnUrl(): ?string
    {
        return $this->returnUrl;
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