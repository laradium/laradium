<?php

namespace Netcore\Aven\Aven;

use File;
use Illuminate\Http\Request;
use Netcore\Aven\Traits\Crud;
use Netcore\Aven\Traits\Datatable;

abstract class AbstractAvenResource
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
     * AbstractAvenResource constructor.
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

        return view('aven::admin.resource.index', compact('table', 'model', 'resource'));
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
        $response = $form->formatedResponse();

        return ([
            'languages' => collect(translate()->languages())->map(function ($item, $index) {
                $item['is_current'] = $index == 0;

                return $item;
            })->toArray(),
            'inputs'    => $response,
            'tabs'      => $resource->fieldSet()->tabs()->toArray()
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
        $form->buildForm();

        return view('aven::admin.resource.create', compact('form'));
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
        $form->buildForm();

        return view('aven::admin.resource.edit', compact('form'));
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
     * @return \Netcore\Aven\Aven\Resource
     */
    abstract protected function resource();

    /**
     * @return Table
     */
//    abstract protected function table();
}