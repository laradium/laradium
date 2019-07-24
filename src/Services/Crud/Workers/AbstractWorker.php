<?php

namespace Laradium\Laradium\Services\Crud\Workers;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Services\Crud\CrudDataHandler;

abstract class AbstractWorker
{
    /**
     * @var Model
     */
    public $model;

    /**
     * @var string
     */
    public $relation;

    /**
     * @var array
     */
    public $formData;

    /**
     * @var CrudDataHandler
     */
    public $crudDataHandler;

    /**
     * AbstractWorker constructor.
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
     * @return array
     */
    public function getData(): array
    {
        return [
            $this->relation => $this->formData
        ];
    }

    /**
     * Before save
     */
    abstract public function beforeSave(): void;

    /**
     * After save
     */
    abstract public function afterSave(): void;
}