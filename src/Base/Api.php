<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;

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
    public function model($model)
    {
        $this->model = $model;

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
}