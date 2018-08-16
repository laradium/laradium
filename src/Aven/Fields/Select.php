<?php

namespace Netcore\Aven\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Select extends Field
{

    /**
     * @var string
     */
    protected $view = 'aven::admin.fields.select';

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

    public function formatedResponse($field = null)
    {
        $field = !is_null($field) ? $field : $this;

        return [
            'type'    => strtolower(array_last(explode('\\', get_class($field)))),
            'name'    => $field->getNameAttribute(),
            'label'   => $field->getLabel(),
            'options' => collect($field->getOptions())->map(function ($text, $value) use($field) {
                return [
                    'value'    => $value,
                    'text'     => $text,
                    'selected' => $field->getValue() == $value,
                ];
            })->toArray(),
        ];
    }
}