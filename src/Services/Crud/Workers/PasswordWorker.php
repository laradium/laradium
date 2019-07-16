<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Services\Crud\CrudDataHandler;

class PasswordWorker implements WorkerInterface
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
        foreach ($this->formData as $fieldName => $value) {
            if (!str_contains($fieldName, '_confirmation') && $value) {
                $this->model->update([
                    $fieldName => bcrypt($value)
                ]);
            }
        }
    }
}