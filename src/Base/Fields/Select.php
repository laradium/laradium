<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;

class Select extends Field
{

    /**
     * @var FieldSet
     */
    protected $fieldSet;

    /**
     * Select constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->fieldSet = new FieldSet();
    }

    /**
     * @var array
     */
    protected $options = [
        '' => '- Select -'
    ];

    /**
     * @var
     */
    protected $onChange;

    /**
     * @var array
     */
    protected $languages = [];

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options += $options;

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
     * @param array $onChange
     * @return $this
     */
    public function onChange(array $onChange, array $languages)
    {
        $this->onChange = $onChange;
        $this->languages = $languages;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function getOnChange()
    {
        $fieldSet = $this->fieldSet->setModel($this->model());

        return [
            'fields'    => collect($this->onChange)->mapWithKeys(function ($closure, $index) use ($fieldSet) {
                $closure($fieldSet);

                return [
                    $index => $fieldSet->fields()->map(function ($field) {
                        return $field->formattedResponse();
                    })
                ];
            }),
            'languages' => collect($this->languages)->mapWithKeys(function ($languages, $index) {
                foreach ($languages as $i => $language) {
                    $lang[] = [
                        'id'          => $language->id,
                        'iso_code'    => $language->iso_code,
                        'is_fallback' => !!$language->is_fallback,
                        'is_current'  => $i === 0
                    ];
                }

                return [
                    $index => $lang
                ];
            })
        ];
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

        return [
            'type'                  => strtolower(array_last(explode('\\', get_class($field)))),
            'name'                  => !empty($field->getNameAttribute()) ? $field->getNameAttribute() : $field->name(),
            'label'                 => $field->getLabel(),
            'default'               => $field->getDefault(),
            'isHidden'              => $field->isHidden(),
            'replacementAttributes' => $attributes->toArray(),
            'tab'                   => $this->tab(),
            'value'                 => !empty($this->getValue()) ? $this->getValue() : $this->getDefault(),
            'col'                   => $this->col,
            'attr'                  => $this->getAttr(),
            'options'               => collect($field->getOptions())->map(function ($text, $value) use ($field) {
                return [
                    'value'    => $value,
                    'text'     => $text,
                    'selected' => $field->getValue() === $value,
                ];
            })->toArray(),
            'onChange'              => $this->getOnChange()
        ];
    }
}