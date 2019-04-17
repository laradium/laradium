<?php

namespace Laradium\Laradium\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class AttachmentResource extends JsonResource
{

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->file_file_name,
            'url'       => $this->file->url(),
            'is_image'  => $this->isImageMimeType($this->file_content_type),
            'extension' => pathinfo($this->file_file_name, PATHINFO_EXTENSION)
        ];
    }

    /**
     * @param string $mimeType
     * @return bool
     */
    private function isImageMimeType(string $mimeType): bool
    {
        $mimeTypes = [
            'image/gif',
            'image/jpeg',
            'image/pjpeg',
            'image/png',
            'image/svg+xml',
            'image/tiff',
            'image/vnd.microsoft.icon',
            'image/vnd.wap.wbmp ',
            'image/webp',
        ];

        return in_array($mimeType, $mimeTypes);
    }
}
