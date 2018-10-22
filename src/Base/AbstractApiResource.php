<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response(function () {
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

            $model = $model->paginate(10);

            $model->getCollection()->transform(function ($row, $key) use ($api) {
                foreach ($api->fields() as $field) {
                    $value = $field['modify'] ? $field['modify']($row) : $row->{$field['name']};

                    $attributes[$field['name']] = $value;
                }

                return $attributes;
            });

            return response()->json([
                'success' => true,
                'data'    => $this->parseData($model)
            ]);
        });
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function create()
    {
        return $this->response(function () {
            $model = $this->model;

            $resource = $this->resource();
            $form = new Form($resource->setModel($model)->build());
            $form->buildForm();

            return response()->json([
                'success' => true,
                'data'    => $form->formattedResponse()
            ]);
        });
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->response(function () use ($request) {
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

            return response()->json([
                'success' => true,
                'message' => 'Resource successfully created!'
            ]);
        });
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        return $this->response(function () use ($id) {
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
                $value = $field['modify'] ? $field['modify']($model) : $model->{$field['name']};

                return [$field['name'] => $value];
            });

            return response()->json([
                'success' => true,
                'data'    => $data
            ]);
        });
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id)
    {
        return $this->response(function () use ($id) {
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
                'data'    => $form->formattedResponse()
            ]);
        });
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        return $this->response(function () use ($request, $id) {
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

            return response()->json([
                'success' => true,
                'message' => 'Resource successfully updated!'
            ]);
        });
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        return $this->response(function () use ($id) {
            $model = $this->model;
            $api = $this->api()->setModel($model);

            if ($api->getWhere()) {
                $model = $model->where($api->getWhere());
            }

            $model = $model->findOrFail($id);

            $model->delete();

            return response()->json([
                'success' => true,
                'message' => 'Resource successfully deleted!'
            ]);
        });
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
     * @param callable $func
     * @return \Illuminate\Http\JsonResponse
     */
    protected function response(callable $func)
    {
        try {
            return call_user_func($func);
        } catch (\Exception $e) {
            if ($e instanceof ModelNotFoundException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found.'
                ], 404);
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed.',
                    'errors'  => $e->errors()
                ], 422);
            }

            logger()->error($e);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error! Please, try again.'
            ], 503);
        }
    }

    /**
     * @param $data
     * @return array
     */
    protected function parseData($data)
    {
        if ($data instanceof LengthAwarePaginator) {
            return [
                'items' => $data->getCollection(),
                'meta'  => [
                    'current_page' => $data->currentPage(),
                    'last_page'    => $data->lastPage(),
                    'total'        => $data->total()
                ]
            ];
        }

        return $data;
    }

    /**
     * @return \Laradium\Laradium\Base\Api
     */
    abstract protected function api();
}