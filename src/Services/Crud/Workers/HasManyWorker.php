<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use ReflectionException;

class HasManyWorker extends AbstractWorker
{

    /**
     * @return array
     */
    public function beforeSave(): void
    {
        //
    }

    /**
     * @throws ReflectionException
     */
    public function afterSave(): void
    {
        foreach ($this->formData as $item) {
            // if array has "id" field, it means that this is existing entry, if not, it's new
            if ($id = array_get($item, 'id')) {
                $relationModel = $this->model->{$this->relation}()->find($id);

                // If entry has remove field, it means that it must be deleted
                if (array_get($item, 'remove')) {
                    $relationModel->delete();
                }

                continue;
            }

            // We get base data in order to create child
            $baseData = collect($item)->filter(function ($value) {
                return !is_array($value);
            })->toArray();

            if (!empty($this->formData)) {
                dd($this->model);
            }

            $relationModel = $this->model->{$this->relation}()->create($baseData);

            // Remove everything which is not base data because we have already saved it
            $item = collect($item)->filter(function ($value) {
                return is_array($value);
            })->toArray();

            // save data recursively
            $this->crudDataHandler->saveData(array_except($item, 'id'), $relationModel);
        }
    }
}