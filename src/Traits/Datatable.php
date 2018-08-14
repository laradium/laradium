<?php

namespace Netcore\Aven\Traits;

use Yajra\DataTables\Facades\DataTables;

trait Datatable {

    /**
     * @param Request $request
     * @return array
     */
    public function editable(Request $request)
    {
        $this->model->where('id', $request->get('pk'))->update([$request->get('name') => $request->get('value')]);

        return [
            'state' => 'success'
        ];
    }

    /**
     * @return mixed
     */
    public function dataTable()
    {
        $table = $this->table();
        $resourceName = $this->model->getTable();
        if (count($table->getRelations())) {
            $model = $this->model->with($table->getRelations())->select('*');
        } else {
            $model = $this->model->select('*');
        }

        $dataTable = DataTables::of($model);

        $columns = $table->columns();
        $editableColumns = $columns->where('editable', true);

        foreach ($editableColumns as $column) {
            $dataTable->editColumn($column['column_parsed'], function ($item) use ($column, $resourceName) {
                return '<a href="#" 
                class="js-editable" 
                data-name="' . $column['column_parsed'] . '"
                data-type="text" 
                data-pk="' . $item->id . '" 
                data-url="/admin/' . $resourceName . '/editable" 
                data-title="Enter value">' . $item->{$column['column_parsed']} . '</a>';
            });
        }

        $dataTable->addColumn('action', function ($item) {
            return view('aven::admin.resource._partials.action', compact('item'))->render();
        });

        $rawColumns = ['action'];

        if ($editableColumns->count()) {
            $rawColumns = array_merge($rawColumns, $editableColumns->pluck('column')->toArray());
        }

        $dataTable->rawColumns($rawColumns);


        return $dataTable->make(true);
    }
}