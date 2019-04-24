<?php

namespace Laradium\Laradium\Http\Controllers;

use Czim\Paperclip\Attachment\Attachment;

class ResourceController
{

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
