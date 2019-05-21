<?php

namespace Laradium\Laradium\Traits;

use ReflectionException;

trait Crud
{
    use Worker;

    /**
     * @var array
     */
    private $unwantedKeys = ['crud_worker', 'morphable_type', 'morphable_name'];

    /**
     * @param $inputs
     * @param $model
     * @return bool
     * @throws ReflectionException
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
                if ($crudWorkerClass === \Laradium\Laradium\Base\Fields\HasMany::class || $crudWorkerClass === \Laradium\Laradium\Base\Fields\HasOne::class) {
                    $this->hasManyWorker($model, $key, array_except($worker, 'crud_worker'));
                } else if ($crudWorkerClass === \Laradium\Laradium\Base\Fields\Password::class) {
                    $this->passwordWorker($model, array_except($worker, 'crud_worker'));
                } elseif ($crudWorkerClass === \Laradium\Laradium\Base\Fields\MorphTo::class) {
                    $this->morphToWorker($model, array_except($worker, 'crud_worker'));
                } elseif ($crudWorkerClass === \Laradium\Laradium\Base\Fields\BelongsToMany::class) {
                    $this->belongsToManyToWorker($model, $key, array_except($worker, 'crud_worker'));
                }
            }
        }

        return $model;
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
        foreach ($array as $index => &$value) {
            if(in_array($index, $this->unwantedKeys)) {
                unset($array[$index]);
            }

            if (is_array($value)) {
                if (array_get($value, 'remove', null)) {
                    unset($array[$index]);
                }
                $this->recursiveUnset($value, $this->unwantedKeys);
            }
        }

        return true;
    }
}