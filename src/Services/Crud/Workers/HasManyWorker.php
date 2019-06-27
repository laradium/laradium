<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Services\Crud\CrudDataHandler;
use ReflectionException;

class HasManyWorker implements WorkerInterface
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
     * HasManyWorker constructor.
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
     * @throws ReflectionException
     */
    public function handle(): void
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