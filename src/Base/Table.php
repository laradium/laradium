<?php

namespace Laradium\Laradium\Base;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Laradium\Laradium\Services\Datatable;
use Throwable;

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
     * @var Model
     */
    protected $model;

    /**
     * @var string
     */
    protected $additionalView;

    /**
     * @var array
     */
    protected $additionalViewData;

    /**
     * @var Closure
     */
    protected $where;

    /**
     * @var string
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
     * @var Closure
     */
    protected $search;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var AbstractResource
     */
    private $resource;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $toggleUrl;

    /**
     * Table constructor.
     */
    public function __construct()
    {
        $this->columnSet = new ColumnSet;
    }

    /**
     * @return string
     * @throws Throwable
     */
    public function render(): string
    {
        return view('laradium::admin.table.index', [
            'table' => $this
        ])->render();
    }

    /**
     * @return array
     */
    public function getTableConfig(): array
    {
        return [
            'columns'      => $this->getColumnConfig(),
            'base_columns' => $this->columns(),
            'order'        => isset($this->getOrderBy()['key']) ? ['[' . $this->getOrderBy()['key'] . ', "' . $this->getOrderBy()['direction'] . '"]'] : ['[0, "desc"]'],
            'url'          => $this->getUrl(),
            'toggle_url'   => $this->getToggleUrl(),
        ];
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function data(): JsonResponse
    {
        $datatable = new Datatable($this);

        return $datatable->make();
    }

    /**
     * @param array $relations
     * @return $this
     */
    public function relations(array $relations): self
    {
        $this->relations = $relations;

        return $this;
    }

    /**
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
    }

    /**
     * @param Closure $closure
     * @return $this
     */
    public function make(Closure $closure): self
    {
        $closure($this->columnSet);

        return $this;
    }

    /**
     * @param $model
     * @return $this
     */
    public function model($model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel(): Model
    {
        return $this->model;
    }

    /**
     * @return Collection
     */
    public function columns(): Collection
    {
        return $this->columnSet->list;
    }

    /**
     * @return array
     */
    public function getRawColumnList(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['raw'] === true;
        })->pluck('column_parsed')->toArray();
    }

    /**
     * @return array
     */
    public function getAddedColumns(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['new'] === true;
        })->toArray();
    }

    /**
     * @return array
     */
    public function getEditableColumns(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['editable'] === true && $column['translatable'] === false;
        })->toArray();
    }

    /**
     * @return array
     */
    public function getTranslatableColumns(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['translatable'] === true && $column['editable'] === false;
        })->toArray();
    }

    /**
     * @return array
     */
    public function getTranslatableColumnsWithEditable(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['translatable'] === true && $column['editable'] === true;
        })->toArray();
    }

    /**
     * @return array
     */
    public function getModifiedColumns(): array
    {
        return $this->columnSet->list->filter(function ($column) {
            return $column['new'] === false && $column['modify'];
        })->toArray();
    }

    /**
     * @param string $value
     * @return Table
     */
    public function title(string $value): self
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        if ($this->title) {
            return $this->title;
        }

        $model = new $this->model;

        return ucfirst(str_replace('_', ' ', $model->getTable()));
    }

    /**
     * @param string $value
     * @return Table
     */
    public function url(string $value): self
    {
        $this->url = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }


    /**
     * @param string $value
     * @return Table
     */
    public function toggleUrl(string $value): self
    {
        $this->toggleUrl = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getToggleUrl(): ?string
    {
        return $this->toggleUrl;
    }

    /**
     * @param string $value
     * @return Table
     */
    public function slug(string $value): self
    {
        $this->slug = $value;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): string
    {
        if (!$this->getResource()) {
            return $this->slug ?? $this->getModel()->getTable();
        }

        if ($this->getResource()->isShared()) {
            return '/' . $this->getResource()->getBaseResource()->getSlug();
        }

        return '/admin/' . $this->getResource()->getBaseResource()->getSlug();
    }


    /**
     * @param string $value
     * @param array $data
     * @return Table
     */
    public function additionalView(string $value, array $data = []): self
    {
        $this->additionalView = $value;
        $this->additionalViewData = $data;

        return $this;
    }

    /**
     * @return string
     */
    public function getAdditionalView(): string
    {
        return $this->additionalView;
    }

    /**
     * @return array
     */
    public function getAdditionalViewData(): array
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
     * @param AbstractResource $value
     * @return Table
     */
    public function resource(AbstractResource $value): self
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
                'name'       => $column['column'],
                'searchable' => !($column['translatable'] || $column['not_searchable']),
                'orderable'  => !($column['translatable'] || $column['not_sortable']),
            ]);
        }

        return $config;
    }

    /**
     * @param Closure $closure
     * @return $this
     */
    public function where(Closure $closure): self
    {
        $this->where = $closure;

        return $this;
    }

    /**
     * @param string $column
     * @param string $direction
     * @return Table
     */
    public function orderBy(string $column, string $direction = 'desc'): Table
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
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @param Closure $closure
     * @return $this
     */
    public function search(Closure $closure): self
    {
        $this->search = $closure;

        return $this;
    }

    /**
     * @return Closure
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

        return in_array('edit', $actions, true) || in_array('destroy', $actions, true);
    }
}
