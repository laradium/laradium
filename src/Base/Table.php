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
        'column'    => 'id',
        'direction' => 'desc'
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
     * @var
     */
    protected $search;

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
    public function dataTable($value)
    {
        $this->dataTable = $value;

        return $this;
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
                'title'      => $column['title'] ?? $this->parseTitle($column['column']),
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
            'class'      => 'text-center'
        ]);

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
        //was in a hurry couldn't remember if there exists a cleaner way, so feel free to optimize this
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
            'column'    => $column,
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
     * @param \Closure $closure
     * @return $this
     */
    public function search(\Closure $closure)
    {
        $this->search = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getSearch()
    {
        return $this->search;
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

        $this->orderBy = [
            'key'       => 0,
            'column'    => $this->getSortableColumn(),
            'direction' => 'asc'
        ];

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

    /**
     * @param $title
     * @return string
     */
    protected function parseTitle($title): string
    {
        $title = str_replace(['.', '_'], ' ', $title);
        $title = ucwords($title);

        return $title;
    }
}