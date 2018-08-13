<?php

namespace Netcore\Aven\Aven;

use Illuminate\Http\Request;

abstract class AbstractAvenResource
{

    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $resource;

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

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $fields = collect($request->except('_token'));
        $resourceData = $this->getResourceData($fields);
        $this->updateResource($resourceData, $model);

        return back()->withSuccess('Resource successfully created!');
    }

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

        $validationRules = $form->getValidationRules();
        $request->validate($validationRules);

        $fields = collect($request->except('_token'));
        $resourceData = $this->getResourceData($fields);
        $relationList = $resourceData['relationList'];
        $model = $this->model->with(array_except($relationList, 'translations'))->find($id);

        $this->updateResource($resourceData, $model);

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
     * @param $resourceData
     * @param $model
     * @return bool
     * @throws \ReflectionException
     */
    public function updateResource($resourceData, $model)
    {
        $resourceFields = $resourceData['resourceFields'];
        $relations = $resourceData['relations'];
        $translations = $resourceData['translations'];

        $model->fill($resourceFields->toArray());
        $model->save();
        $this->putTranslations($model, $translations);

        $this->updateRelations($relations, $model);

        return true;
    }

    /**
     * @param $fields
     * @return array
     */
    public function getResourceData($fields): array
    {
        $resourceFields = $fields->filter(function ($item) {
            return !is_array($item);
        });

        $relations = $fields->filter(function ($item) {
            return is_array($item);
        });

        $translations = $fields->filter(function ($item, $index) {
            return is_array($item) && $index == 'translations';
        })->toArray();

        $relationList = array_keys($relations->toArray());


        return compact('resourceFields', 'relations', 'relationList', 'translations');
    }

    /**
     * @param $relations
     * @param $model
     * @throws \ReflectionException
     */
    public function updateRelations($relations, $model)
    {
        foreach (array_except($relations, 'translations') as $relationName => $relationSet) {
            $existingItemSet = collect($relationSet)->filter(function ($item) {
                return isset($item['id']);
            })->toArray();
            $nonExistingItemSet = collect($relationSet)->filter(function ($item) {
                return !isset($item['id']);
            })->toArray();

            $relationModel = $model->{$relationName}();
            $relationType = (new \ReflectionClass($relationModel))->getShortName();

            if (count($nonExistingItemSet)) {
                if ($relationType == 'HasMany') {
                    foreach($nonExistingItemSet as $item) {
                        $newItem = $relationModel->create(array_except($item, 'translations'));
                        $this->putTranslations($newItem, array_only($item, 'translations'));
                    }
                }
            }

            if (count($existingItemSet)) {
                if ($relationType == 'HasMany') {
                    foreach ($existingItemSet as $item) {
                        $relationModel = $model->{$relationName}()->find($item['id']);
                        $relationModel->fill(array_except($item, 'translations'));
                        $relationModel->save();
                        $this->putTranslations($relationModel, array_only($item, 'translations'));
                    }
                }
            }
        }
    }

    /**
     * @param $model
     * @param $translations
     * @return bool
     */
    protected function putTranslations($model, $translations)
    {
        if (isset($translations['translations'])) {
            $translations = $translations['translations'];
            if(count($translations)) {
                foreach ($translations as $locale => $translationList) {
                    $translation = $model->translations()->firstOrCreate(['locale' => $locale]);
                    $translation->fill($translationList);
                    $translation->save();
                }
            }
        }

        return true;
    }

    /**
     * @return array
     */
    abstract protected function resource();

    /**
     * @return array
     */
    abstract protected function table();
}