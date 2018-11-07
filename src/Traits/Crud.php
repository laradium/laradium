<?php

namespace Laradium\Laradium\Traits;

trait Crud
{

    /**
     * @var array
     */
    private $unwantedKeys = ['crud_worker'];

    /**
     * @param $inputs
     * @param $model
     * @return bool
     * @throws \ReflectionException
     */
    public function saveData($inputs, $model)
    {
        // Update or create base model
        $baseData = collect($inputs)->filter(function ($value, $index) {
            return $index !== 'translations' && !is_array($value);
        })->toArray();

        if ($model->exists) {
            $model->update($baseData);
        } else {
            $model = $model->create($baseData);
        }

        // Update or create translations
        $translations = collect($inputs)->filter(function ($value, $index) {
            return $index === 'translations';
        })->toArray();

        $this->putTranslations($translations, $model);

        // Run workers for relations or custom fields (HasMany, HasOne, MorphTo)
        $workers = collect($inputs)->filter(function ($value, $index) {
            return $index !== 'translations' && is_array($value);
        })->toArray();

        foreach ($workers as $key => $worker) {
            if ($crudWorkerClass = array_get($worker, 'crud_worker', null)) {
                if ($crudWorkerClass === \Laradium\Laradium\Base\Fields\HasMany::class && !in_array($key, ['password']) || $crudWorkerClass === \Laradium\Laradium\Base\Fields\HasOne::class && !in_array($key, ['password'])) {
                    $this->hasManyWorker($model, $key, array_except($worker, 'crud_worker'));
                } elseif ($crudWorkerClass == \Laradium\Laradium\Base\Fields\Password::class) {
                    $this->passwordWorker($model, array_except($worker, 'crud_worker'));
                }
            }
        }

        return $model;
    }

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
            if(!str_contains($fieldName, '_confirmation') && $value) {
                $model->update([
                    $fieldName => bcrypt($value)
                ]);
            }
        }
    }

    /**
     * @param $data
     * @param $model
     */
    private function putTranslations($data, $model)
    {
        $model->fill(array_get($data, 'translations', []));
        $model->save();
    }

    /**
     * @param $request
     * @return \Illuminate\Http\Request
     */
    private function prepareRequest($request)
    {
        $data = $request->all();
        $this->recursiveUnset($data);

        return (new \Illuminate\Http\Request($data));
    }

    /**
     * @param $array
     * @return bool
     */
    private function recursiveUnset(&$array)
    {
        foreach ($this->unwantedKeys as $key) {
            unset($array[$key]);
            foreach ($array as $index => &$value) {
                if (is_array($value)) {
                    if (array_get($value, 'remove', null)) {
                        unset($array[$index]);
                    }
                    $this->recursiveUnset($value, $this->unwantedKeys);
                }
            }
        }

        return true;
    }
}