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
            if ($item == '__ID__') {
                return '__ID' . ($index + 1) . '__';
            } else {
                return $item;
            }
        });

        $field->setNameAttributeList($attributes->toArray());

        $attributes = $attributes->filter(function ($item) {
            return str_contains($item, '__ID');
        });

        $url = null;
        $size = null;
        $name = null;

        if (!$field->isTranslatable()) {
            if ($this->model->{$this->name} && $this->model->{$this->name}->exists()) {
                $url = $this->model->{$this->name}->url();
                $size = number_format($this->model->{$this->name}->size() / 1000, 2);
                $name = $this->model->{$this->name}->originalFilename();
            }

            $data = [
                'type'                   => 'file',
                'name'                   => $field->getNameAttribute(),
                'label'                  => $field->getLabel(),
                'isTranslatable'         => $field->isTranslatable(),
                'replacemenetAttributes' => $attributes->toArray(),
                'tab'                    => $this->tab(),
                'col'                    => $this->col,
                'url'                    => $url,
                'file_name'              => $name,
                'file_size'              => $size,
            ];
        } else {
            $data = [
                'type'                   => strtolower(array_last(explode('\\', get_class($field)))),
                'label'                  => $field->getLabel(),
                'isTranslatable'         => $field->isTranslatable(),
                'replacemenetAttributes' => $attributes->toArray(),
                'tab'                    => $this->tab(),
                'col'                    => $this->col,
            ];
            $translatedAttributes = [];

            foreach (translate()->languages() as $language) {
                $field->setLocale($language->iso_code);
                $model = $field->model()->translations->where('locale', $language->iso_code)->first();
                if ($model && $model->{$this->name} && $model->{$this->name}->exists()) {
                    $url = $model->{$this->name}->url();
                    $size = number_format($model->{$this->name}->size() / 1000, 2);
                    $name = $model->{$this->name}->originalFilename();
                }

                $translatedAttributes[] = [
                    'iso_code'  => $language->iso_code,
                    'value'     => $field->getValue(),
                    'name'      => $field->getNameAttribute(),
                    'url'       => $url,
                    'file_name' => $name,
                    'file_size' => $size,
                ];
            }

            $data['translatedAttributes'] = $translatedAttributes;
        }

        return $data;
    }
}