<?php

namespace Laradium\Laradium\Base;

use Illuminate\Http\Request;
use Laradium\Laradium\Traits\ApiResponse;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;

abstract class AbstractApiResource
{
    use Crud, CrudEvent, ApiResponse;

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $actions = [
        'index',
        'show'
    ];

    /**
     * @var int
     */
    protected $paginate = 10;

    /**
     * AbstractApiResource constructor.
     */
    public function __construct()
    {
        $this->model(new $this->resource);
        $this->events = collect([]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->response(function () {
            $model = $this->model;
            $api = $this->api()->model($model);

            if (count($api->getRelations())) {
                $model = $model->with($api->getRelations())->select('*');
            } else {
                $model = $model->select('*');
            }

            if ($api->getWhere()) {
                $model = $model->where($api->getWhere());
            }

            if ($this->paginate) {
                $model = $model->paginate($this->paginate);
                $model->getCollection()->transform(function ($row, $key) use ($api) {
                    return $this->getFields($row);
                });
            } else {
                $model = $model->get();
                $model->transform(function ($row) {
                    return $this->getFields($row);
                });
            }

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
            $form = $this->getForm();

            return response()->json([
                'success' => true,
                'data'    => [
                    'fields' => $form->response()
                ]
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
            $form = $this->getForm();
            $validationRequest = $this->prepareRequest($request);

            $this->fireEvent('beforeSave', $request);

            $validationRules = $form->getValidationRules();
            $validationRequest->validate($validationRules);

            $model = $this->saveData($request->all(), $this->getModel());

            $form->model($model);
            $this->model($model);

            $this->fireEvent('afterSave', $request);

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
            $api = $this->api()->model($model);

            if (count($api->getRelations())) {
                $model = $model->with($api->getRelations())->select('*');
            } else {
                $model = $model->select('*');
            }

            if ($api->getWhere()) {
                $model = $model->where($api->getWhere());
            }

            $model = $model->findOrFail($id);
            $data = $this->getFields($model);

            return response()->json([
                'success' => true,
                'data'    => $this->parseData($data)
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
            $api = $this->api()->model($model);

            if ($api->getWhere()) {
                $model = $model->where($api->getWhere());
            }

            $model = $model->findOrFail($id);

            $form = $this->getForm($model);

            return response()->json([
                'success' => true,
                'data'    => [
                    'fields' => $form->response()
                ]
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
            $api = $this->api()->model($model);

            if ($api->getWhere()) {
                $model = $model->where($api->getWhere());
            }

            $this->model($model = $model->findOrFail($id));

            $form = $this->getForm();
            $validationRequest = $this->prepareRequest($request);

            $this->fireEvent('beforeSave', $request);

            $validationRules = $form->getValidationRules();
            $validationRequest->validate($validationRules);

            $this->saveData($request->all(), $this->getModel());

            $this->fireEvent('afterSave', $request);

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
            $api = $this->api()->model($model);

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
     * @param null $model
     * @return Resource
     */
    public function getBaseResource($model = null)
    {
        $model = $model ?? $this->getModel();

        return (new ApiResource)->model($model)
            ->name($this->name)
            ->slug($this->slug);
    }

    /**
     * @param null $model
     * @return Form
     */
    private function getForm($model = null)
    {
        $form = (new Form(
            $this
                ->getBaseResource($model ?? $this->getModel())
                ->make($this->resource()->closure())
                ->build())
        )->build();

        return $form;
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
     * @return mixed
     */
    public function getModel()
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
     * @param $row
     * @return array
     */
    protected function getFields($row)
    {
        $fields = [];
        $api = $this->api()->model($this->model);

        foreach ($api->fields() as $field) {
            $value = $field['modify'] ? $field['modify']($row) : $row->{$field['name']};

            $fields[$field['name']] = $value;
        }

        return $fields;
    }

    /**
     * @return \Laradium\Laradium\Base\Api
     */
    abstract protected function api();
}