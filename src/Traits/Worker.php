<?php

namespace Laradium\Laradium\Traits;

trait Worker
{

    /**
     * @param $model
     * @param $relation
     * @param $items
     * @throws \ReflectionException
     */
    private function hasManyWorker($model, $relation, $items)
    {
        foreach ($items as $item) {
            // if array has "id" field, it means that this is existing entry, if not, it's new
            if ($id = array_get($item, 'id', null)) {
                $relationModel = $model->{$relation}()->find($id);

                // If entry has remove field, it means that it must be deleted
                if (array_get($item, 'remove', null)) {
                    $relationModel->delete();
                    continue;
                }
            } else {
                // We get base data in order to create child
                $baseData = collect($item)->filter(function ($value) {
                    return !is_array($value);
                })->toArray();

                $relationModel = $model->{$relation}()->create($baseData);

                // Remove everything which is not base data because we have already saved it
                $item = collect($item)->filter(function ($value) {
                    return is_array($value);
                })->toArray();
            }

            // save data recursively
            $this->saveData(array_except($item, 'id'), $relationModel);
        }
    }

    /**
     * @param $model
     * @param $passwords
     */
    private function passwordWorker($model, $passwords)
    {
        foreach ($passwords as $fieldName => $value) {
            if (!str_contains($fieldName, '_confirmation') && $value) {
                $model->update([
                    $fieldName => bcrypt($value)
                ]);
            }
        }
    }

    /**
     * @param $model
     * @param $data
     * @throws \ReflectionException
     */
    private function morphToWorker($model, $data)
    {
        $morphableName = array_get($data, 'morphable_name');
        $morphableType = array_get($data, 'morphable_type');

        $fields = array_except($data, ['morphable_name', 'morphable_type']);

        if (!count($fields)) {
            $this->saveMorphToData($model, $morphableType, $morphableName, []);
        }

        foreach ($fields as $key => $value) {
            if ($id = array_get($value, 'id', null)) {
                $this->saveData($value, $model->{$morphableName});
            } else {
                $this->saveMorphToData($model, $morphableType, $morphableName, $value);
            }
        }
    }

    /**
     * @param $model
     * @param $relationName
     * @param $data
     */
    private function belongsToManyToWorker($model, $relationName, $data)
    {
        $checked = collect($data)->filter(function ($value, $key) {
            return !is_array($value);
        })->mapWithKeys(function ($value, $key) {
            return [$value => $value];
        });

        $pivot = collect($data['pivot'] ?? [])->filter(function ($value, $key) use ($checked) {
            return is_array($value) && $checked->contains($key);
        })->mapWithKeys(function ($value, $key) {
            return [$key => $value];
        });

        $data = array_replace($checked->all(), $pivot->all());

        $model->{$relationName}()->sync($data);
    }

    /**
     * @param $model
     * @param $morphableType
     * @param $morphableName
     * @param $data
     * @return void
     */
    private function saveMorphToData($model, $morphableType, $morphableName, $data)
    {
        $morphableModel = new $morphableType;
        $createdMorphableModel = $this->saveData($data, $morphableModel);

        $model->{$morphableName . '_id'} = $createdMorphableModel->id;
        $model->{$morphableName . '_type'} = $morphableType;
        $model->save();
    }

}