<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;
use Laradium\Laradium\Base\Field;

class Button extends Field
{
    private $withFormGroup = true;

    /**
     * Button constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->fieldName(array_get($parameters, 0, ''));

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getFieldName(),
            'slug'   => str_slug($this->getFieldName(), '_'),
            'type'   => $this->getType(),
            'value'  => $this->getValue(),
            'attr'   => $this->getAttr(),
            'config' => [
                'col'             => $this->getCol(),
                'with_form_group' => $this->withFormGroup
            ]
        ];
    }

    /**
     * @return $this
     */
    public function withoutFormGroup(): self
    {
        $this->withFormGroup = false;

        return $this;
    }
}
