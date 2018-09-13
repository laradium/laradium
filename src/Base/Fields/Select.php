<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Select extends Field
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

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

        return [
            'type'                   => strtolower(array_last(explode('\\', get_class($field)))),
            'name'                   => $field->getNameAttribute(),
            'label'                  => $field->getLabel(),
            'default'                => $field->getDefault(),
            'isHidden'               => $field->isHidden(),
            'replacemenetAttributes' => $attributes->toArray(),
            'tab'                    => $this->tab(),
            'col'                    => $this->col,
            'options'                => collect($field->getOptions())->map(function ($text, $value) use ($field) {
                return [
                    'value'    => $value,
                    'text'     => $text,
                    'selected' => $field->getValue() == $value,
                ];
            })->toArray(),
        ];
    }
}