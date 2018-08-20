<?php

namespace Netcore\Aven\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController
{

    public function index()
    {
        return redirect()->to('/admin/dashboard');
    }

    /**
     * @return mixed
     */
    public function dashboard()
    {
        return view(config('aven.dashboard-view'));
    }

    public function resourceDelete(Request $request, $id)
    {
        $resource = $request->get('resource', null);
        if ($resource) {
            $model = new $resource;
            $model->find($id)->delete();
        }

        return [
            'state' => 'success'
        ];
    }
}
