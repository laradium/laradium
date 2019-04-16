<?php

namespace Laradium\Laradium\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Laradium\Laradium\Http\Requests\AttachmentRequest;
use Laradium\Laradium\Http\Resources\AttachmentResource;
use Laradium\Laradium\Models\Attachment;

class AttachmentController
{
    private const ATTACHMENTS_PER_PAGE = 12;

    /**
     * @param AttachmentRequest $request
     * @return JsonResponse
     */
    public function store(AttachmentRequest $request): JsonResponse
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

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function upload(Request $request): AnonymousResourceCollection
    {
        $files = $request->file('files');

        foreach($files as $file) {
            Attachment::create([
                'file' => $file
            ]);
        }

        return $this->attachments();
    }

    /**
     * @return AnonymousResourceCollection
     */
    public function attachments(): AnonymousResourceCollection
    {
        $attachments = $this->getAttachmnetCollection();

        return AttachmentResource::collection($attachments);
    }

    /**
     * @return LengthAwarePaginator
     */
    private function getAttachmnetCollection(): LengthAwarePaginator
    {
        if($search = request()->get('search')) {
            return Attachment::where('file_file_name', 'LIKE', '%' . $search . '%')->orderBy('id', 'desc')->paginate(self::ATTACHMENTS_PER_PAGE);
        }

        return Attachment::orderBy('id', 'desc')->paginate(self::ATTACHMENTS_PER_PAGE);
    }

    /**
     * @param Attachment $attachment
     * @return JsonResponse
     * @throws \Exception
     */
    public function destroy(Attachment $attachment): JsonResponse
    {
        // delete attachment itself
        $attachment->file->setToBeDeleted();
        $attachment->save();

        // delete databe entry
        $attachment->delete();

        return response()->json([
            'success' => true,
        ]);
    }
}
