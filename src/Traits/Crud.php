<?php

namespace Netcore\Aven\Traits;

trait Crud {

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
                    foreach ($nonExistingItemSet as $item) {
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
            if (count($translations)) {
                foreach ($translations as $locale => $translationList) {
                    $translation = $model->translations()->firstOrCreate(['locale' => $locale]);
                    $translation->fill($translationList);
                    $translation->save();
                }
            }
        }

        return true;
    }
}