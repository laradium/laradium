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
        $fieldName = $this->getFieldName();

        $data['worker'] = (new Hidden('crud_worker', $this->getModel()))
            ->build([$this->getNameAttribute()])
            ->value(get_class($this))
            ->formattedResponse();

        if (!$this->isTranslatable()) {
            if ($model->{$fieldName} && $model->{$fieldName}->exists()) {
                $url = $this->getUrl($model, 'get');
                $size = number_format($model->{$fieldName}->size() / 1000, 2);
                $name = $model->{$fieldName}->originalFilename();
                $deleteUrl = $this->getUrl($model, 'destroy');
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
     * @return string
     */
    public function getUrl($model, $action = 'get')
    {
        $fieldName = $this->getFieldName();
        $attachment = $model->{$fieldName};
        $storage = $attachment->getConfig()['storage'] ?? '';
        $url = encrypt([get_class($model), $model->id, $fieldName]);

        return $storage === 'local' ? route($this->isShared() ? 'resource.' . $action . '-file' : 'admin.resource.' . $action . '-file', $url) : $attachment->url();
    }

    /**
     * @return array
     */
    public function getTranslations(): array
    {
        $translations = [];
        $model = $this->getModel();
        $fieldName = $this->getFieldName();

        if ($this->isTranslatable()) {
            $attributes = $this->getAttributes();
            unset($attributes[count($attributes) - 1]);
            foreach (translate()->languages() as $language) {
                $isoCode = $language->iso_code;
                $this->build(array_merge($attributes, ['translations', $isoCode]));
                $model = $this->getModel()->translateOrNew($isoCode);

                $url = null;
                $size = null;
                $name = null;
                $deleteUrl = null;

                if ($model->{$fieldName} && $model->{$fieldName}->exists()) {
                    $url = $this->getUrl($model, 'get');
                    $size = number_format($model->{$fieldName}->size() / 1000, 2);
                    $name = $model->{$fieldName}->originalFilename();
                    $deleteUrl = $this->getUrl($model, 'destroy');
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