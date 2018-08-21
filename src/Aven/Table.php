<?php

namespace Netcore\Aven\Aven;

class Table
{

    protected $relations = [];
    protected $columnSet;
    protected $model;
    protected $additionalView;
    protected $additionalViewData;
    protected $actions = ['create', 'edit', 'delete'];

    public function __construct()
    {
        $this->columnSet = new ColumnSet;
    }

    /**
     * @param $relatioins
     * @return $this
     */
    public function relations($relatioins)
    {
        $this->relations = $relatioins;

        return $this;
    }

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
        $closure($this->columnSet);

        return $this;
    }

    public function setModel($model)
    {
        $this->model = $model;

        return $this;
    }

    public function model()
    {
        return $this->model;
    }

    public function columns()
    {
        return $this->columnSet->list;
    }

    public function actions($value)
    {
        $this->actions = $value;

        return $this;
    }

    public function hasAction($value)
    {
        return in_array($value, $this->actions);
    }

    public function additionalView($value, $data)
    {
        $this->additionalView = $value;
        $this->additionalViewData = $data;

        return $this;
    }

    public function getAdditionalView()
    {
        return $this->additionalView;
    }

    public function getAdditionalViewData()
    {
        return $this->additionalViewData;
    }
}