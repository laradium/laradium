<?php

namespace Laradium\Laradium\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController
{

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return redirect()->to('/admin/dashboard');
    }

    /**
     * @return mixed
     */
    public function dashboard()
    {
        return view(config('laradium.dashboard-view'));
    }

    /**
     * @param Request $request
     * @param $id
     * @return array
     */
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
