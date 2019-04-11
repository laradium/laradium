<?php

namespace Laradium\Laradium\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Laradium\Laradium\Http\Requests\AttachmentRequest;
use Laradium\Laradium\Models\Attachment;

class AttachmentController
{

    /**
     * @param AttachmentRequest $request
     * @return RedirectResp«onse
     */
    public function store(AttachmentRequest $request)
    {
        $file = $request->file('file');

        $attachment = Attachment::create([
            'file' => $file
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'url' => $attachment->file->url()
            ]
        ]);
    }

    public function attachments()
    {
        return response()->json([
            'state' => 'success',
            'code'  => 200,
            'data'  => Attachment::get()->map(function ($attachment) {
                return [
                    'id'   => $attachment->id,
                    'name' => $attachment->file_file_name,
                    'url'  => $attachment->file->url(),
                ];
            })
        ]);
    }
}
