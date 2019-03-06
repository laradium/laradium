<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;
use Laradium\Laradium\Http\Controllers\Admin\DatatableController;
use Laradium\Laradium\Services\Asset\AssetManager;

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
     * @var
     */
    private $title;

    /**
     * @var array
     */
    protected $orderBy = [
        'column'    => 'id',
        'direction' => 'desc'
    ];

    /**
     * @var
     */
    protected $search;

    /**
     * @var
     */
    private $slug;

    /**
     * @var
     */
    private $resource;

    /**
     * @var AssetManager
     */
    private $assetManager;

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->assetManager = app(AssetManager::class);
        $this->columnSet = new ColumnSet;
    }

    /**
     * @return mixed
     */
    public function render()
    {
        return view('laradium::admin.table.index', [
            'table' => $this
        ])->render();
    }

    /**
     * @return mixed
     */
    public function config()
    {
        return $this->assetManager->table()->config($this);
    }

    /**
     * @return string
     */
    public function getTableConfig(): string
    {
        $config = [
            'id'       => $this->getResourceId(),
            'columns'  => $this->getColumnConfig(),
            'order'    => isset($this->getOrderBy()['key']) ? ['[' . $this->getOrderBy()['key'] . ', "' . $this->getOrderBy()['direction'] . '"]'] : ['[0, "desc"]'],
            'slug'     => $this->getSlug(),
            'has_tabs' => false,
            'selector' => '.' . $this->getResourceId(),
        ];

        if ($this->getTabs()) {
            $config['selector'] = '.tab-pane.active .' . $this->getResourceId();
            $config['has_tabs'] = true;
        }

        return json_encode($config);
    }

    /**
     * @return mixed
     */
    public function data()
    {
        $datatableController = new DatatableController();

        return $datatableController->index($this);
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
    public function model($model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
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
     * @return Table
     */
    public function title($value): self
    {
        $this->title = $value;

        return $this;
    }

    public function getTitle()
    {
        if ($this->title) {
            return $this->title;
        }

        $model = new $this->model;

        return ucfirst(str_replace('_', ' ', $model->getTable()));
    }

    /**
     * @param $value
     * @return Table
     */
    public function slug($value): self
    {
        $this->slug = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if (!$this->getResource()) {
            return $this->slug;
        }

        if ($this->getResource()->isShared()) {
            return '/' . $this->getResource()->getBaseResource()->getSlug();

        }

        return '/admin/' . $this->getResource()->getBaseResource()->getSlug();
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return trim(str_replace('/', '-', $this->getSlug()), '-');
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
     * @param $value
     * @return Table
     */
    public function resource($value): self
    {
        $this->resource = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @return Collection
     */
    public function getColumnConfig(): Collection
    {
        $config = collect([]);

        foreach ($this->columns() as $column) {
            $config->push([
                'data'       => $column['column'],
                'name'       => $column['translatable'] ? 'translations.' . $column['column'] : $column['column'],
                'searchable' => $column['translatable'] || $column['not_searchable'] ? false : true,
                'orderable'  => $column['translatable'] || $column['not_sortable'] ? false : true,
            ]);
        }

        if ($this->columns()->where('column', 'action')->first() || !$this->getResource()) {
            return $config;
        }

        if (!$this->hasActions()) {
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
     * @return bool
     */
    public function hasActions(): bool
    {
        $actions = $this->getResource()->getActions();

        return in_array('edit', $actions) || in_array('destroy', $actions);
    }
}