<?php

namespace Laradium\Laradium\Services;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Interfaces\ResourceFilterInterface;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Facades\DataTables;

class Datatable
{

    /**
     * @var Table
     */
    private $table;

    /**
     * @var EloquentDataTable
     */
    private $dataTable;

    /**
     * Datatable constructor.
     * @param Table $table
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * @return JsonResponse
     * @throws Exception
     */
    public function make(): JsonResponse
    {
        $model = $this->getFilteredModel();

        $this->dataTable = DataTables::of($model);

        $this->dataTable->rawColumns($this->table->getRawColumnList());

        $this->addAddedColumns();

        $this->editModifiedColumns();

        $this->addEditableColumns();

        $this->addTranslatableColumns();

        $this->addTranslatableColumnsWithEditable();

        $this->applyCustomSearch();

        $this->applyCustomOrderColumn();

        return $this->dataTable->make();
    }

    /**
     * @return void
     */
    private function applyCustomOrderColumn(): void
    {
        if (!request()->get('order')) {
            $this->dataTable->order(function ($query) {
                $orderBy = $this->table->getOrderBy();

                $query->orderBy($orderBy['column'], $orderBy['direction']);
            });
        }
    }

    /**
     * @return void
     */
    private function applyCustomSearch(): void
    {
        if ($this->table->getSearch()) {
            $this->dataTable->filter($this->table->getSearch());
        }
    }

    /**
     * @return void
     */
    private function addAddedColumns(): void
    {
        $addedColumns = $this->table->getAddedColumns();
        if (!count($addedColumns)) {
            return;
        }

        foreach ($addedColumns as $column) {
            $this->dataTable->addColumn($column['column_parsed'], $column['modify']);
        }
    }

    /**
     * @return void
     */
    private function editModifiedColumns(): void
    {
        $modifiedColumns = $this->table->getModifiedColumns();

        if (!count($modifiedColumns)) {
            return;
        }

        foreach ($modifiedColumns as $column) {
            $this->dataTable->editColumn($column['column_parsed'], $column['modify']);
        }
    }

    /**
     * @return void
     */
    private function addEditableColumns(): void
    {
        $editableColumns = $this->table->getEditableColumns();
        $slug = $this->table->getSlug();

        foreach ($editableColumns as $column) {
            $this->dataTable->editColumn($column['column_parsed'], function ($item) use ($column, $slug) {
                return view('laradium::admin.table._partials.editable', compact('item', 'column', 'slug'))->render();
            });
        }
    }

    /**
     * @return void
     */
    private function addTranslatableColumns(): void
    {
        $translatableColumns = $this->table->getTranslatableColumns();

        foreach ($translatableColumns as $column) {
            $this->dataTable->addColumn($column['column_parsed'], function ($item) use ($column) {
                return view('laradium::admin.resource._partials.translation', compact('item', 'column'))->render();
            });
        }
    }

    /**
     * @return void
     */
    private function addTranslatableColumnsWithEditable(): void
    {
        $translatableColumns = $this->table->getTranslatableColumnsWithEditable();
        $slug = $this->table->getSlug();

        foreach ($translatableColumns as $column) {
            $this->dataTable->addColumn($column['column_parsed'], function ($item) use ($column, $slug) {
                return view('laradium::admin.resource._partials.translation_editable',
                    compact('item', 'column', 'slug'))
                    ->render();
            });
        }
    }

    /**
     * @return Builder
     */
    private function getModel(): Builder
    {
        if (count($this->table->getRelations())) {
            return $this->table->getModel()->with($this->table->getRelations())->select($this->table->getModel()->getTable() . '.*');
        }

        return $this->table->getModel()->select($this->table->getModel()->getTable() . '.*');
    }

    /**
     * @return Builder
     */
    private function getFilteredModel(): Builder
    {
        $model = $this->getModel();
        $resource = $this->table->getResource();
        if ($resource && $where = $resource->getBaseResource()->getWhere()) {
            $model = $model->where($where);
        }

        if ($this->table->getWhere()) {
            $model = $model->where($this->table->getWhere());
        }

        if ($resource instanceof ResourceFilterInterface) {
            $model = $resource->filter($model);
        }

        return $model;
    }
}