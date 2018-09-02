<?php

namespace Netcore\Aven\Aven;

use Illuminate\Support\Collection;

/**
 * Class Api
 * @package Netcore\Aven\Aven
 */
class Api
{

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var ApiFieldSet
     */
    protected $fieldSet;

    /**
     * @var
     */
    protected $model;

    /**
     * @var array
     */
    protected $routes = ['index', 'store', 'show', 'update', 'delete'];

    /**
     * @var array
     */
    protected $customRoutes = [];

    /**
     * @var array
     */
    protected $middleware = [];

    /**
     * @var
     */
    protected $where;

    /**
     * Table constructor.
     */
    public function __construct($model)
    {
        $this->model = $model;
        $this->fieldSet = new ApiFieldSet;
    }

    /**
     * @param $relations
     * @return $this
     */
    public function relations($relations)
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations()
    {
        return $this->relations;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function make(\Closure $closure)
    {
        $closure($this->fieldSet);

        return $this;
    }

    /**
     * @param $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return mixed
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function columns()
    {
        return $this->fieldSet->list;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function where(\Closure $closure)
    {
        $this->where = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param $routes
     * @return $this
     */
    public function routes($routes)
    {
        $this->routes = $routes;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param $routes
     * @return $this
     */
    public function customRoutes($routes)
    {
        $this->customRoutes = $routes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCustomRoutes()
    {
        return $this->customRoutes;
    }

    /**
     * @param $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        $this->middleware = $middleware;

        return $this;
    }

    /**
     * @return array
     */
    public function getMiddleware()
    {
        return $this->middleware;
    }

    /**
     * @param null $id
     * @return Collection
     */
    public function getData($id = null)
    {
        if ($id) {
            if (count($this->getRelations())) {
                $model = $this->model->with($this->getRelations())->select('*');
            } else {
                $model = $this->model->select('*');
            }

            if ($this->getWhere()) {
                $model = $this->model->where($this->getWhere());
            }

            $model = $model->findOrFail($id);

            return $this->fieldSet->list->mapWithKeys(function ($field) use ($model) {
                $value = $field['modify'] ?? $model->{$field['name']};

                return [$field['name'] => $value];
            });
        }

        if (count($this->getRelations())) {
            $model = $this->model->with($this->getRelations())->select('*');
        } else {
            $model = $this->model->select('*');
        }

        if ($this->getWhere()) {
            $model = $this->model->where($this->getWhere());
        }

        $model = $model->get();

        return $model->map(function ($row, $key) {
            foreach ($this->fieldSet->list as $field) {
                $value = $field['modify'] ?? $row->{$field['name']};

                $attributes[$field['name']] = $value;
            }

            return $attributes;
        });
    }
}