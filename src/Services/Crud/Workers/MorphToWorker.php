<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use ReflectionException;

class MorphToWorker extends AbstractWorker
{

    /**
     * @return array
     */
    public function beforeSave(): array
    {
        return $this->formData;
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function afterSave(): void
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