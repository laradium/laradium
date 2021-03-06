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
     * @param $url
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function getFile($url)
    {
        list($model, $id, $file) = decrypt($url);

        $model = $model::findOrFail($id);

        abort_unless($model->{$file}->exists(), 404);

        return response()->download(storage_path('app/' . $model->{$file}->path()));
    }

    /**
     * @param $url
     * @return array
     */
    public function destroyFile($url)
    {
        list($model, $id, $file) = decrypt($url);

        $model = $model::findOrFail($id);
        $model->{$file} = Attachment::NULL_ATTACHMENT;
        $model->save();

        if (request()->ajax()) {
            return [
                'state' => 'success'
            ];
        }

        return back()->withSuccess('Resource file successfully deleted!');
    }
}
