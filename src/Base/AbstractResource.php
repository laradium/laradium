<?php

namespace Laradium\Laradium\Base;

use File;
use Illuminate\Http\Request;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\Datatable;

abstract class AbstractResource
{
    use Crud, Datatable;

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var array
     */
    protected $events = [];

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * AbstractResource constructor.
     */
    public function __construct()
    {
        $this->model = new $this->resource;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $model = $this->model;
        $table = $this->table()->setModel($model);
        $resource = $this;
        $name = $this->getName();

        return view('laradium::admin.resource.index', compact('table', 'model', 'resource', 'name'));
    }

    /**
     * @param null $id
     * @return array
     */
    public function getForm($id = null)
    {
        if ($id) {
            $model = $this->model->find($id);
        } else {
            $model = $this->model;
        }

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->buildForm();
        $response = $form->formattedResponse();

        return ([
            'languages'      => $this->languages(),
            'inputs'         => $response,
            'tabs'           => $resource->fieldSet()->tabs()->toArray(),
            'isTranslatable' => $form->isTranslatable()
        ]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        $model = $this->model;

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->abstractResource($this);
        $form->buildForm();

        $name = $this->getName();
        $slug = $this->getSlug();

        return view('laradium::admin.resource.create', compact('form', 'name', 'slug'));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \ReflectionException
     */
    public function store(Request $request)
    {
        $model = $this->model;

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->buildForm();

        if (isset($this->events['beforeSave'])) {
            $this->events['beforeSave']($this->model, $request);
        }

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $this->updateResource($request->except('_token'), $model);

        if (isset($this->events['afterSave'])) {
            $this->events['afterSave']($this->model, $request);
        }

        if ($request->ajax()) {
            return [
                'success'  => 'Resource successfully created',
                'redirect' => url()->previous()
            ];
        }

        return back()->withSuccess('Resource successfully created!');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->model->findOrNew($id);

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->abstractResource($this);
        $form->buildForm();

        $name = $this->getName();
        $slug = $this->getSlug();

        return view('laradium::admin.resource.edit', compact('form', 'name', 'slug'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     */
    public function update(Request $request, $id)
    {
        $model = $this->model->findOrNew($id);

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->buildForm();

        if (isset($this->events['beforeSave'])) {
            $this->events['beforeSave']($this->model, $request);
        }

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $model = $this->model->find($id);

        $this->updateResource($request->except('_token'), $model);

        if (isset($this->events['afterSave'])) {
            $this->events['afterSave']($this->model, $request);
        }

        if ($request->ajax()) {
            return [
                'success' => 'Resource successfully updated',
                'data'    => $this->getForm($model->id)
            ];
        }

        return back()->withSuccess('Resource successfully updated!');

    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function destroy(Request $request, $id)
    {
        $model = $this->model->find($id);
        $model->delete();

        if ($request->ajax()) {
            return [
                'state' => 'success'
            ];
        }

        return back()->withSuccess('Resource successfully deleted!');
    }

    /**
     * @param $name
     * @param \Closure $callable
     * @return $this
     */
    protected function registerEvent($name, \Closure $callable)
    {
        $this->events[$name] = $callable;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResourceName()
    {
        return $this->model->getTable();
    }

    /**
     * @return mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @return bool
     */
    public function importInProgress(): bool
    {
        return !!File::exists(storage_path('app/import/' . $this->model->getTable() . '-import.lock'));
    }

    /**
     * @return bool|string
     */
    public function importStatus()
    {
        return file_get_contents(storage_path('app/import/' . $this->model->getTable() . '-import.lock'));
    }

    /**
     * @return mixed
     */
    public function languages()
    {
        return translate()->languages()->map(function ($item, $index) {
            $item->is_current = $index === 0;

            return $item;
        })->toArray();
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if (!$this->slug && $this->name) {
            $this->slug = strtolower(str_replace(' ', '-', $this->name));

            return $this->slug;
        } else if (!$this->slug && !$this->name) {
            $this->name = str_replace('_', '-', $this->model->getTable());

            return $this->name;
        }

        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name && !$this->slug) {
            return ucfirst(str_replace('_', ' ', $this->model->getTable()));
        } else if (!$this->name && $this->slug) {
            return ucfirst(str_replace('-', ' ', $this->slug));
        }

        return ucfirst($this->name);
    }

    /**
     * @return \Laradium\Laradium\Base\Resource
     */
    abstract protected function resource();

    /**
     * @return Table
     */
//    abstract protected function table();
}