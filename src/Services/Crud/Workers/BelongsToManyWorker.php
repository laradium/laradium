<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Services\Crud\CrudDataHandler;

class BelongsToManyWorker implements WorkerInterface
{

    /**
     * @var Model
     */
    private $model;

    /**
     * @var string
     */
    private $relation;

    /**
     * @var array
     */
    private $formData;

    /**
     * @var CrudDataHandler
     */
    private $crudDataHandler;

    /**
     * PasswordWorker constructor.
     * @param CrudDataHandler $crudDataHandler
     * @param Model $model
     * @param string $relation
     * @param array $formData
     */
    public function __construct(CrudDataHandler $crudDataHandler, Model $model, string $relation, array $formData)
    {
        $this->crudDataHandler = $crudDataHandler;
        $this->model = $model;
        $this->relation = $relation;
        $this->formData = $formData;
    }

    /**
     * @return void
     */
    public function handle(): void
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