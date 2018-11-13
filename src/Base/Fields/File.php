<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class File extends Field
{

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $model = $this->getModel();

        $data['worker'] = (new Hidden('crud_worker', $this->getModel()))
            ->build([$this->getNameAttribute()])
            ->value(get_class($this))
            ->formattedResponse();

        if (!$this->isTranslatable()) {
            if ($model->{$this->getFieldName()} && $model->{$this->getFieldName()}->exists()) {
                $url = $model->{$this->getFieldName()}->url();
                $size = number_format($model->{$this->getFieldName()}->size() / 1000, 2);
                $name = $model->{$this->getFieldName()}->originalFilename();
                $deleteUrl = route('admin.resource.destroy-file', [
                    get_class($model), $model->id, $this->getFieldName()
                ]);
            }

            $data['file'] = [
                'url'       => $url ?? null,
                'file_name' => $name ?? null,
                'file_size' => $size ?? null,
                'deleteUrl' => $deleteUrl ?? null
            ];
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        $translations = [];
        $model = $this->getModel();

        if ($this->isTranslatable()) {
            foreach (translate()->languages() as $language) {
                $isoCode = $language->iso_code;
                $this->build(['translations', $isoCode]);
                $model = $this->getModel()->translateOrNew($isoCode);

                $url = null;
                $size = null;
                $name = null;
                $deleteUrl = null;

                if ($model && $model->{$this->getFieldName()} && $model->{$this->getFieldName()}->exists()) {
                    $url = $model->{$this->getFieldName()}->url();
                    $size = number_format($model->{$this->getFieldName()}->size() / 1000, 2);
                    $name = $model->{$this->getFieldName()}->originalFilename();
                    $deleteUrl = route('admin.resource.destroy-file', [
                        get_class($model), $model->id, $this->getFieldName(), $language->iso_code
                    ]);
                }

                $translations[] = [
                    'iso_code' => $isoCode,
                    'value'    => $this->getValue(),
                    'name'     => $this->getNameAttribute(),
                    'file'     => [
                        'url'       => $url,
                        'file_name' => $name,
                        'file_size' => $size,
                        'deleteUrl' => $deleteUrl
                    ]
                ];
            }

            $this->model($model);
        }

        return $translations;
    }
}