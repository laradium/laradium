<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Hidden extends Field
{

    /**
     * @var string
     */
    protected $class = '';

    /**
     * @var
     */
    protected $params;

    /**
     * @param $value
     * @return $this
     */
    public function class($value)
    {
        $this->class = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param $params
     * @return $this
     */
    public function params($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key)
    {
        return array_get($this->params, $key, null);
    }

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

        $data = [
            'type'                  => $field->getParam('orderable') ? 'hidden-sortable' : strtolower(array_last(explode('\\',
                get_class($field)))),
            'name'                  => $field->getNameAttribute(),
            'value'                 => $field->getValue(),
            'replacementAttributes' => $attributes->toArray(),
            'attr'                  => $this->getAttr(),
        ];

        if ($field->getParam('orderable')) {
            $data['orderable'] = true;
        }

        return $data;
    }
}