<?php

namespace Laradium\Laradium\Http\Controllers\Admin;

use Czim\Paperclip\Attachment\Attachment;
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

    /**
     * @param Request $request
     * @param $model
     * @param $id
     * @param $file
     * @param null $locale
     * @return array
     */
    public function destroyFile(Request $request, $model, $id, $file, $locale = null)
    {
        $model = $model::findOrFail($id);
        $model->{$file} = Attachment::NULL_ATTACHMENT;
        $model->save();

        if ($request->ajax()) {
            return [
                'state' => 'success'
            ];
        }

        return back()->withSuccess('Resource file successfully deleted!');
    }

    /**
     * @param $value
     * @return \Illuminate\Http\RedirectResponse
     */
    public function setBelongsTo($value = null)
    {
        laradium()->belongsTo()->set($value);

        return back();
    }
}
