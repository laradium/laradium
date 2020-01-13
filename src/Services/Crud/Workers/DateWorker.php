<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Carbon\Carbon;

class DateWorker extends AbstractWorker
{
    /**
     * @return array
     */
    public function beforeSave(): void
    {
        foreach ($this->formData as $fieldName => $value) {
            $this->formData[$fieldName] = $value ? Carbon::parse($value) : null;
        }
    }

    /**
     * @return void
     */
    public function afterSave(): void
    {
        //
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->formData;
    }
}
