<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Traits\ApiResponse;
use Laradium\Laradium\Traits\Crud;
use Laradium\Laradium\Traits\CrudEvent;

abstract class AbstractApiResource
{
    /**
     * @var
     */
    protected $model;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var array
     */
    protected $customRoutes = [];

    /**
     * AbstractApiResource constructor.
     */
    public function __construct()
    {
        $this->model(new $this->resource);
    }

    /**
     * @param null $model
     * @return Resource
     */
    public function getBaseResource($model = null)
    {
        $model = $model ?? $this->getModel();

        return (new ApiResource)->model($model)
            ->name($this->name)
            ->slug($this->slug);
    }

    /**
     * @param $value
     * @return $this
     */
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return array
     */
    public function getCustomRoutes()
    {
        return $this->customRoutes;
    }

    /**
     * @return string
     */
    public function resourceName()
    {
        return $this->resource;
    }
}