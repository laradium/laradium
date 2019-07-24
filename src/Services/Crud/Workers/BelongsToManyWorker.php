<?php

namespace Laradium\Laradium\Services\Crud\Workers;

class BelongsToManyWorker extends AbstractWorker
{

    /**
     * @return void
     */
    public function beforeSave(): void
    {
        //
    }

    /**
     * @return void
     */
    public function afterSave(): void
    {
        $checked = collect($this->formData)->filter(function ($value, $key) {
            return !is_array($value);
        })->mapWithKeys(function ($value, $key) {
            return [$value => $value];
        });

        $pivot = collect($this->formData['pivot'] ?? [])->filter(function ($value, $key) use ($checked) {
            return is_array($value) && $checked->contains($key);
        })->mapWithKeys(function ($value, $key) {
            return [$key => $value];
        });

        $data = array_replace($checked->all(), $pivot->all());

        $this->model->{$this->relation}()->sync($data);
    }
}