<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class FormSubmit extends Element
{

    /**
     * @var string
     */
    private $label = 'Save';

    /**
     * Block constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, $model)
    {
        $this->setName(array_first($parameters));

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'form_name' => $this->getName(),
            'type'      => 'form-submit',
            'attr'      => $this->getAttributes(),
            'label'     => $this->getLabel(),
            'config'    => [
                'col' => $this->getName(),
            ],
        ];
    }

    /**
     * @return string
     */
    private function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param $value
     * @return $this
     */
    public function label($value): self
    {
        $this->label = $value;

        return $this;
    }
}
