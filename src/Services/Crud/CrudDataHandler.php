<?php

namespace Laradium\Laradium\Services\Crud;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Laradium\Laradium\Base\Fields\BelongsToMany;
use Laradium\Laradium\Base\Fields\Date;
use Laradium\Laradium\Base\Fields\DateTime;
use Laradium\Laradium\Base\Fields\HasMany;
use Laradium\Laradium\Base\Fields\HasOne;
use Laradium\Laradium\Base\Fields\MorphTo;
use Laradium\Laradium\Base\Fields\Password;

class CrudDataHandler
{

    /**
     * Removeable keys
     */
    private const UNWANTED_KEYS = ['crud_worker', 'morphable_type', 'morphable_name'];

    /**
     * Workers
     */
    private const WORKERS = [
        HasMany::class,
        Password::class,
        MorphTo::class,
        BelongsToMany::class,
        HasOne::class,
        DateTime::class,
        Date::class
    ];

    /**
     * @param array $formData
     * @param Model $model
     * @return Model
     */
    public function saveData(array $formData, Model $model): Model
    {
        $unmodifiedData = $formData;
        $baseModel = $model;
        // Run workers for relations or custom fields (HasMany, HasOne, MorphTo)
        $workers = $this->getWorkers($unmodifiedData, $model);
        foreach ($workers as $worker) {
            $worker->beforeSave();

            $formData = array_merge($unmodifiedData, $worker->getData());
        }

        // Update or create base model
        $baseData = collect($formData)->filter(function ($value, $index) {
            return $index !== 'translations' && !is_array($value);
        })->toArray();

        if ($model->exists) {
            $model->update($baseData);
        } else {
            $model = $model->create($baseData);
        }

        // Update or create translations
        $translations = collect($formData)->filter(function ($value, $index) {
            return $index === 'translations';
        })->toArray();

        $this->putTranslations($translations, $model);

        $workers = $this->getWorkers($unmodifiedData, $model);

        foreach ($workers as $worker) {
            $worker->afterSave();
        }

        return $model;
    }

    /**
     * @param string $class
     * @return string
     */
    private function getWorkerName(string $class): string
    {
        return '\Laradium\Laradium\Services\Crud\Workers\\' . class_basename($class) . 'Worker';
    }

    /**
     * @param $data
     * @param $model
     */
    private function putTranslations($data, $model): void
    {
        $model->fill(array_get($data, 'translations', []));
        $model->save();
    }

    /**
     * @param $request
     * @return Request
     */
    public function prepareRequest($request): Request
    {
        $data = $request->all();
        $this->recursiveUnset($data);

        return (new Request($data));
    }

    /**
     * @param $array
     * @return bool
     */
    private function recursiveUnset(&$array): bool
    {
        foreach ($array as $index => &$value) {
            if (is_string($index) && in_array($index, self::UNWANTED_KEYS, false)) {
                unset($array[$index]);
            }

            if (is_array($value)) {
                if (array_get($value, 'remove', null)) {
                    unset($array[$index]);
                }

                $this->recursiveUnset($value);
            }
        }

        return true;
    }

    /**
     * @param $formData
     * @param $model
     * @return array
     */
    private function getWorkers($formData, $model)
    {
        $workerInstances = [];
        $workers = collect($formData)->filter(function ($value, $index) {
            return $index !== 'translations' && is_array($value);
        })->toArray();

        foreach ($workers as $key => $worker) {
            $crudWorkerClass = array_get($worker, 'crud_worker');
            if (!$crudWorkerClass) {
                continue;
            }

            $workerClass = $this->getWorkerName($crudWorkerClass);
            $formData = array_except($worker, 'crud_worker');

            $workerInstances[] = (new $workerClass($this, $model, $key, $formData));
        }

        return $workerInstances;
    }
}
