<?php

namespace Netcore\Aven\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Hidden extends Field
{

    protected $class = '';
    protected $params;

    /**
     * @var string
     */
    protected $view = 'aven::admin.fields.hidden';

    public function class($value)
    {
        $this->class = $value;

        return $this;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function params($params)
    {
        $this->params = $params;

        return $this;
    }

    public function getParam($key)
    {
        return array_get($this->params, $key, null);
    }

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

        $data = [
            'type'  => $field->getParam('orderable') ? 'hidden-sortable' : strtolower(array_last(explode('\\',
                get_class($field)))),
            'name'  => $field->getNameAttribute(),
            'value' => $field->getValue(),
            'replacemenetAttributes' => $attributes->toArray()
        ];
        if ($field->getParam('orderable')) {
            $data['orderable'] = true;
        }

        return $data;
    }
}