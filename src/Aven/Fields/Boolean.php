<?php

namespace Netcore\Aven\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Boolean extends Field
{

    /**
     * @var string
     */
    protected $view = 'aven::admin.fields.boolean';

    public function formatedResponse($field = null)
    {
        $field = !is_null($field) ? $field : $this;
        return [
            'type'    => strtolower(array_last(explode('\\', get_class($field)))),
            'name'    => $field->getNameAttribute(),
            'label'   => $field->getLabel(),
            'checked' => $field->getValue() == 1,
        ];
    }
}