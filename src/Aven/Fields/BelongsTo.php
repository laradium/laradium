<?php

namespace Netcore\Aven\Aven\Fields;

use Illuminate\Database\Eloquent\Model;
use Netcore\Aven\Aven\Field;

class BelongsTo extends Field
{

    /**
     * @var
     */
    protected $relationModel;

    /**
     * @var
     */
    protected $title;

    /**
     * BelongsTo constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationModel = new $this->name;
        $this->label = array_last(explode('\\', $this->name));
        $this->name = strtolower($this->label) . '_id';

    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->relationModel->all()->pluck(($this->title ?: 'name'), 'id')->toArray();
    }

    /**
     * @param $value
     * @return $this
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
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
            'type'                   => 'select',
            'name'                   => $field->getNameAttribute(),
            'label'                  => $field->getLabel(),
            'replacementAttributes'  => $attributes->toArray(),
            'isHidden'               => $field->isHidden(),
            'default'                => $field->getDefault(),
            'tab'                    => $this->tab(),
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