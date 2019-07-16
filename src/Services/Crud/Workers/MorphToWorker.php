<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Services\Crud\CrudDataHandler;
use ReflectionException;

class MorphToWorker implements WorkerInterface
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
     * MorphToWorker constructor.
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
     * @throws ReflectionException
     */
    public function handle(): void
    {
        $morphableName = array_get($this->formData, 'morphable_name');
        $morphableType = array_get($this->formData, 'morphable_type');

        $fields = array_except($this->formData, ['morphable_name', 'morphable_type']);

        if (!count($fields)) {
            $this->saveMorphToData($this->model, $morphableType, $morphableName, []);
        }

        foreach ($fields as $key => $value) {
            if ($id = array_get($value, 'id')) {
                $this->crudDataHandler->saveData($value, $this->model->{$morphableName});
            } else {
                $this->saveMorphToData($this->model, $morphableType, $morphableName, $value);
            }
        }
    }

    /**
     * @param Model $model
     * @param string $morphableType
     * @param string $morphableName
     * @param array $data
     * @return void
     */
    private function saveMorphToData(Model $model, string $morphableType, string $morphableName, array $data): void
    {
        $morphableModel = new $morphableType;
        $createdMorphableModel = $this->crudDataHandler($data, $morphableModel);

        $model->{$morphableName . '_id'} = $createdMorphableModel->id;
        $model->{$morphableName . '_type'} = $morphableType;
        $model->save();
    }
}