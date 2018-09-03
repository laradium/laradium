<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class File extends Field
{

    /**
     * @param null $field
     * @return array
     */
    public function formatedResponse($field = null)
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

        if ($this->model->{$this->name} && $this->model->{$this->name}->exists()) {
            $url = $this->model->{$this->name}->url();
            $size = number_format($this->model->{$this->name}->size() / 1000, 2);
            $name = $this->model->{$this->name}->originalFilename();
        }

        return [
            'type'                   => 'file',
            'name'                   => $field->getNameAttribute(),
            'label'                  => $field->getLabel(),
            'replacemenetAttributes' => $attributes->toArray(),
            'url'                    => $url,
            'tab'                    => $this->tab(),
            'file_name'              => $name,
            'file_size'              => $size,
        ];
    }
}