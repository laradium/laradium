<?php

namespace Laradium\Laradium\Base;

use Illuminate\Http\Request;
use Laradium\Laradium\Traits\Crud;

abstract class AbstractApiResource
{
    use Crud;

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
     * @var array
     */
    protected $actions = [
        'index',
        'show'
    ];

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
        $api = $this->api()->setModel($model);

        if (count($api->getRelations())) {
            $model = $model->with($api->getRelations())->select('*');
        } else {
            $model = $model->select('*');
        }

        if ($api->getWhere()) {
            $model = $model->where($api->getWhere());
        }

        $model = $model->get();

        $data = $model->map(function ($row, $key) use ($api) {
            foreach ($api->fields() as $field) {
                $value = $field['modify'] ?? $row->{$field['name']};

                $attributes[$field['name']] = $value;
            }

            return $attributes;
        });

        return response()->json([
            'success' => true,
            'data'    => $data
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

        return response()->json([
            'success' => true,
            'data'    => $form->formatedResponse()
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \ReflectionException
     */
    public function store(Request $request)
    {
        $model = $this->model;

        if (method_exists($this, 'validation')) {
            $validation = $this->validation()->setModel($model)->build();
            $request->validate($validation->getValidationRules());
        } else {
            $form = (new Form($this->resource()->setModel($model)->build()))->buildForm();
            $request->validate($form->getValidationRules());
        }

        if (isset($this->events['beforeSave'])) {
            $this->events['beforeSave']($this->model, $request);
        }

        $this->updateResource($request->except('_token'), $model);

        if (isset($this->events['afterSave'])) {
            $this->events['afterSave']($this->model, $request);
        }

        if ($request->ajax()) {
            return [
                'success' => true,
                'message' => 'Resource successfully created!'
            ];
        }

        return back()->withSuccess('Resource successfully created!');
    }

    /**
     * @param $id
     * @return mixed
     */
    public function show($id)
    {
        $model = $this->model;
        $api = $this->api()->setModel($model);

        if (count($api->getRelations())) {
            $model = $model->with($api->getRelations())->select('*');
        } else {
            $model = $model->select('*');
        }

        if ($api->getWhere()) {
            $model = $model->where($api->getWhere());
        }

        $model = $model->findOrFail($id);

        $data = $api->fields()->mapWithKeys(function ($field) use ($model) {
            $value = $field['modify'] ?? $model->{$field['name']};

            return [$field['name'] => $value];
        });

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function edit($id)
    {
        $model = $this->model;
        $api = $this->api()->setModel($model);

        if ($api->getWhere()) {
            $model = $model->where($api->getWhere());
        }

        $model = $model->findOrFail($id);

        $resource = $this->resource();
        $form = new Form($resource->setModel($model)->build());
        $form->buildForm();

        return response()->json([
            'success' => true,
            'data'    => $form->formatedResponse()
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     * @throws \ReflectionException
     */
    public function update(Request $request, $id)
    {
        $model = $this->model;
        $api = $this->api()->setModel($model);

        if ($api->getWhere()) {
            $model = $model->where($api->getWhere());
        }

        $model = $model->findOrFail($id);

        if (method_exists($this, 'validation')) {
            $validation = $this->validation()->setModel($model)->build();
            $request->validate($validation->getValidationRules());
        } else {
            $form = (new Form($this->resource()->setModel($model)->build()))->buildForm();
            $request->validate($form->getValidationRules());
        }

        if (isset($this->events['beforeSave'])) {
            $this->events['beforeSave']($this->model, $request);
        }

        $this->updateResource($request->except('_token'), $model);

        if (isset($this->events['afterSave'])) {
            $this->events['afterSave']($this->model, $request);
        }

        if ($request->ajax()) {
            return [
                'success' => true,
                'message' => 'Resource successfully updated!'
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
        $model = $this->model;
        $api = $this->api()->setModel($model);

        if ($api->getWhere()) {
            $model = $model->where($api->getWhere());
        }

        $model = $model->findOrFail($id);

        $model->delete();

        if ($request->ajax()) {
            return [
                'success' => true,
                'message' => 'Resource successfully deleted!'
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
     * @param $value
     * @return bool
     */
    public function hasAction($value)
    {
        return in_array($value, $this->actions);
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @return \Laradium\Laradium\Base\Api
     */
    abstract protected function api();
}