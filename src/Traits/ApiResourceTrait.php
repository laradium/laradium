<?php

namespace Netcore\Aven\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResourceTrait
{
    /**
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function apiIndex()
    {
        return response()->json([
            'success' => true,
            'data' => $this->parseData($this->model->paginate(10))
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function apiStore(Request $request)
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
                'success' => true,
                'message' => 'Resource successfully created',
            ];
        }
    }

    /**
     * @param $id
     * @return ApiResource
     */
    public function apiShow($id)
    {
        $model = $this->model->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->parseData($model)
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function apiUpdate(Request $request, $id)
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

        return [
            'success' => true,
            'message' => 'Resource successfully updated',
            'data' => $this->getForm($model->id)
        ];
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
    public function apiDestroy(Request $request, $id)
    {
        $model = $this->model->find($id);
        $model->delete();

        if ($request->ajax()) {
            return [
                'success' => true,
                'message' => 'Resource successfully updated'
            ];
        }
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function parseData($data)
    {
        $fields = collect($this->model->getFillable());
        if ($translatedAttributes = $this->model->translatedAttributes) {
            $fields = $fields->concat($translatedAttributes);
        }

        $fields = $fields->reject(function ($field) {
            return in_array($field, ['locale']) || str_contains($field, '_id');
        })->toArray();

        if ($data instanceof LengthAwarePaginator) {
            return [
                'items' => $data->map(function ($row, $key) use ($fields) {
                    foreach ($fields as $field) {
                        $attributes[$field] = $row->$field;
                    }

                    return $attributes;
                }),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'total' => $data->total()
                ]
            ];
        }

        foreach ($fields as $field) {
            $attributes[$field] = $data->$field;
        }

        return $attributes;
    }
}