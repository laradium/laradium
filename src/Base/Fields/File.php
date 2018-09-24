<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class File extends Field
{

    /**
     * @param null $field
     * @return array
     */
    public function formattedResponse($field = null)
    {
        $field = !is_null($field) ? $field : $this;

        $attributes = collect($field->getNameAttributeList())->map(function ($item, $index) {
            if ($item === '__ID__') {
                return '__ID' . ($index + 1) . '__';
            } else {
                return $item;
            }
        });

        $field->setNameAttributeList($attributes->toArray());

        $attributes = $attributes->filter(function ($item) {
            return str_contains($item, '__ID');
        });

        if (!$field->isTranslatable()) {
            if ($this->model->{$this->name} && $this->model->{$this->name}->exists()) {
                $url = $this->model->{$this->name}->url();
                $size = number_format($this->model->{$this->name}->size() / 1000, 2);
                $name = $this->model->{$this->name}->originalFilename();
                $deleteUrl = route('admin.resource.destroy-file', [
                    get_class($this->model), $this->model->id, $this->name
                ]);
            }

            $data = [
                'type'                  => 'file',
                'name'                  => $field->getNameAttribute(),
                'label'                 => $field->getLabel(),
                'isTranslatable'        => $field->isTranslatable(),
                'replacementAttributes' => $attributes->toArray(),
                'tab'                   => $this->tab(),
                'col'                   => $this->col,
                'attr'                  => $this->getAttr(),
                'url'                   => $url ?? null,
                'deleteUrl'             => $deleteUrl ?? null,
                'file_name'             => $name ?? null,
                'file_size'             => $size ?? null,
            ];
        } else {
            $data = [
                'type'                  => 'file',
                'label'                 => $field->getLabel(),
                'isTranslatable'        => $field->isTranslatable(),
                'replacementAttributes' => $attributes->toArray(),
                'tab'                   => $this->tab(),
                'col'                   => $this->col,
                'attr'                  => $this->getAttr(),
            ];

            $translatedAttributes = [];
            foreach (translate()->languages() as $language) {
                $field->setLocale($language->iso_code);
                $model = $field->model()->translations->where('locale', $language->iso_code)->first();
                if ($model && $model->{$this->name} && $model->{$this->name}->exists()) {
                    $url = $model->{$this->name}->url();
                    $size = number_format($model->{$this->name}->size() / 1000, 2);
                    $name = $model->{$this->name}->originalFilename();
                    $deleteUrl = route('admin.resource.destroy-file', [
                        get_class($this->model), $this->model->id, $this->name, $language->iso_code
                    ]);
                }

                $translatedAttributes[] = [
                    'iso_code'  => $language->iso_code,
                    'value'     => $field->getValue(),
                    'name'      => $field->getNameAttribute(),
                    'url'       => $url ?? null,
                    'deleteUrl' => $deleteUrl ?? null,
                    'file_name' => $name ?? null,
                    'file_size' => $size ?? null,
                ];
            }

            $data['translatedAttributes'] = $translatedAttributes;
        }

        return $data;
    }
}