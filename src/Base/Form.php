<?php

namespace Laradium\Laradium\Base;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Laradium\Laradium\Services\Crud\CrudDataHandler;
use Laradium\Laradium\Traits\CrudEvent;

class Form
{

    use CrudEvent;

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
     * @var CrudDataHandler
     */
    private $crudDataHandler;

    /**
     * Form constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->fields = new Collection;
        $this->events = collect([]);
        $this->crudDataHandler = new CrudDataHandler;
    }

    /**
     * @param Closure $closure
     * @return Form
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
     */
    public function store(Request $request): JsonResponse
    {
        $this->build();
        $validationRequest = $this->crudDataHandler->prepareRequest($request);

        $this->fireEvent(['beforeSave', 'beforeCreate'], $request);
        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);

        $model = $this->crudDataHandler->saveData($request->all(), $this->getModel());
        $this->model($model);

        $this->fireEvent(['afterSave', 'afterCreate'], $request);

        return response()->json($this->data(), 201);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request): JsonResponse
    {
        $this->build();
        $validationRequest = $this->crudDataHandler->prepareRequest($request);
        $this->fireEvent(['beforeSave', 'beforeUpdate'], $request);

        $validationRules = $this->getValidationRules();
        $validationRequest->validate($validationRules);


        $model = $this->crudDataHandler->saveData($request->all(), $this->getModel());
        $this->model($model);

        $this->fireEvent(['afterSave', 'afterUpdate'], $request);

        return response()->json($this->data());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy(Request $request): JsonResponse
    {
        $this->build();
        $this->fireEvent(['beforeDelete'], $request);

        $this->getModel()->delete();

        $this->fireEvent(['afterDelete'], $request);

        return response()->json(null, 204);
    }

    /**
     * @param Request $request
     * @param string|null $locale
     * @return JsonResponse
     */
    public function editable(Request $request, string $locale = null): JsonResponse
    {
        $this->fireEvent(['beforeSave', 'beforeUpdate'], $request);

        $this->updateEditableModel($request, $this->getModel(), $locale);

        $this->fireEvent(['afterSave', 'afterUpdate'], $request);

        return response()->json(null, 204);
    }

    /**
     * @param Request $request
     * @param Model $model
     * @param null|string $locale
     */
    private function updateEditableModel(Request $request, Model $model, string $locale = null): void
    {
        if ($locale) {
            $model->translations()->updateOrCreate([
                'locale' => $locale
            ], [
                'locale'              => $locale,
                $request->get('name') => $request->get('value')
            ]);

            return;
        }

        $model->{$request->get('name')} = $request->get('value');
        $model->save();
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
    public function method($value): Form
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
    public function url($value): Form
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
        if (!$this->redirectTo) {
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
    public function getFormattedFieldResponse(): array
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
    public function getFields(): Collection
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
    public function setValidationRules($rules): self
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
    public function isTranslatable(): bool
    {
        return $this->isTranslatable;
    }
}