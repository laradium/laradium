<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;

class Table
{

    /**
     * @var array
     */
    protected $relations = [];

    /**
     * @var ColumnSet
     */
    protected $columnSet;

    /**
     * @var
     */
    protected $model;

    /**
     * @var
     */
    protected $additionalView;

    /**
     * @var
     */
    protected $additionalViewData;

    /**
     * @var array
     */
    protected $actions = ['create', 'edit', 'delete'];

    /**
     * @var
     */
    protected $where;

    /**
     * @var
     */
    protected $tabs;

    /**
     * @var
     */
    protected $dataTable;

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @var array
     */
    protected $orderBy = [
        'key'       => 0,
        'direction' => 'asc'
    ];

    /**
     * @var boolean
     */
    protected $sortable = false;

    /**
     * @var string
     */
    protected $sortableColumn = 'order';

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->columnSet = new ColumnSet;
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
        $closure($this->columnSet);

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
        return $this->columnSet->list;
    }

    /**
     * @param $value
     * @return $this
     */
    public function actions($value)
    {
        $this->actions = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function dataTable($value)
    {
        $this->dataTable = $value;

        return $this;
    }

    /**
     * @param $value
     * @return bool
     */
    public function hasAction($value)
    {
        return in_array($value, $this->actions);
    }

    /**
     * @param $value
     * @param $data
     * @return $this
     */
    public function additionalView($value, $data = [])
    {
        $this->additionalView = $value;
        $this->additionalViewData = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getAdditionalView()
    {
        return $this->additionalView;
    }

    /**
     * @return mixed
     */
    public function getAdditionalViewData()
    {
        return $this->additionalViewData;
    }

    /**
     * @return mixed
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @return Collection
     */
    public function getColumnConfig(): Collection
    {
        $config = collect([]);

        if ($this->isSortable()) {
            $config->push([
                'data'       => $this->sortableColumn,
                'name'       => $this->sortableColumn,
                'searchable' => false,
                'orderable'  => true,
                'width'      => '2%',
                'class'      => 'text-center'
            ]);
        }

        foreach ($this->columns() as $column) {
            $config->push([
                'data'       => $column['column'],
                'name'       => $column['translatable'] ? 'translations.' . $column['column'] : $column['column'],
                'searchable' => $column['translatable'] || $column['not_searchable'] ? false : true,
                'orderable'  => $column['translatable'] || $column['not_sortable'] ? false : true,
            ]);
        }

        if ($this->columns()->where('column', 'action')->first()) {
            return $config;
        }

        $config->push([
            'data'       => 'action',
            'name'       => 'action',
            'searchable' => false,
            'orderable'  => false,
            'width'      => '15%',
            'class'      => 'text-center'
        ]);

        return $config;
    }

    /**
     * @param $resource
     * @return Collection
     */
    public function getTableConfig($resource)
    {
        $config = collect([
            'processing' => true,
            'serverSide' => true,
            'ajax'       => '/admin/' . $resource->getSlug() . '/data-table',
            'columns'    => $this->getColumnConfig(),
            'order'      => [$this->getOrderBy()['key'], $this->getOrderBy()['direction']]
        ]);

        if ($this->isSortable()) {
            $config->put('rowReorder', [
                'update' => false
            ]);
        }

        return $config;
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
    public function getTabs()
    {
        return $this->tabs;
    }

    /**
     * @param $tabs
     * @return $this
     */
    public function tabs($tabs)
    {
        $this->tabs = $tabs;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function css($assets = [])
    {
        $this->css = $assets;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function js($assets = [])
    {
        $this->js = $assets;

        return $this;
    }

    /**
     * @return array
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * @return array
     */
    public function getJs()
    {
        return $this->js;
    }

    /**
     * @param $column
     * @param string $direction
     * @return Table
     */
    public function orderBy($column, $direction = 'desc')
    {
        if ($this->isSortable()) {
            $this->orderBy = [
                'key'       => 0,
                'direction' => 'asc'
            ];

            return $this;
        }

        // Was in a hurry couldn't remember if there exists a cleaner way, so feel free to optimize this
        $key = -1;
        foreach ($this->columns() as $itemKey => $item) {
            if ($item['column'] === $column) {
                $key = $itemKey;
                break;
            }
        }

        if ($key < 0) {
            return $this;
        }

        $this->orderBy = [
            'key'       => $key,
            'direction' => $direction
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }

    /**
     * @param $value
     * @return $this
     */
    public function sortable($value = null)
    {
        $this->sortable = true;

        if ($value) {
            $this->sortableColumn = $value;
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sortable;
    }

    /**
     * @return string
     */
    public function getSortableColumn()
    {
        return $this->sortableColumn;
    }
}