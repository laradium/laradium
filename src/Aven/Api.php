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
     * @var DataSet
     */
    protected $dataSet;

    /**
     * @var
     */
    protected $model;

    /**
     * @var array
     */
    protected $routes = ['index', 'create', 'store', 'show', 'edit', 'update', 'delete'];

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
    public function __construct()
    {
        $this->dataSet = new DataSet;
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
        $closure($this->dataSet);

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
    public function fields()
    {
        return $this->dataSet->list;
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
}