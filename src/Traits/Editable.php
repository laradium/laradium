<?php

namespace Laradium\Laradium\Traits;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

trait Editable
{

    /**
     * @param Request $request
     * @return array
     */
    public function editable(Request $request)
    {
        $model = $this->getModel()->findOrFail($request->get('pk'));
        $this->model($model);

        return $this->getForm()
            ->events($this->getEvents())
            ->editable($request);
    }
}