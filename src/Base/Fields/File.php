<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class File extends Field
{
    /**
     * @var bool
     */
    private $deletable = true;

    /**
     * @var bool
     */
    private $viewable = false;

    /**
     * @param bool $value
     * @return $this
     */
    public function viewable($value = true): self
    {
        $this->viewable = $value;

        return $this;
    }

    /**
     * @return bool
     */
    private function getViewable(): bool
    {
        return $this->viewable;
    }

    /**
     * @param bool $value
     * @return $this
     */
    public function deletable($value = true): self
    {
        $this->deletable = $value;

        return $this;
    }

    /**
     * @return bool
     */
    private function getDeletable(): bool
    {
        return $this->deletable;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $model = $this->getModel();
        $fieldName = $this->getFieldName();

        $data['worker'] = (new Hidden('crud_worker', $this->getModel()))
            ->build([$this->getName()])
            ->value(get_class($this))
            ->formattedResponse();

        $data['config']['deletable'] = $this->getDeletable();

        if (!$this->isTranslatable()) {
            if ($model->{$fieldName} && $model->{$fieldName}->exists()) {
                $url = $this->getUrl($model);
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
     * @param $model
     * @param string $action
     * @return string
     */
    public function getUrl($model, $action = 'get'): string
    {
        $fieldName = $this->getFieldName();
        $attachment = $model->{$fieldName};
        $storage = $attachment->getConfig()['storage'] ?? '';

        if ($storage === 'local') {
            $url = encrypt([get_class($model), $model->id, $fieldName, $this->getViewable()]);

            return route($this->isShared() ? 'resource.' . $action . '-file' : 'admin.resource.' . $action . '-file', $url);
        }

        return $attachment->url();
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
                    $url = $this->getUrl($model);
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
