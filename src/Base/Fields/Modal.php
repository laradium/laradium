<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class Modal extends Element
{

    /**
     * string|null
     */
    private $title;

    /**
     * Block constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
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
            'name'   => $this->getName(),
            'title'  => $this->getTitle(),
            'type'   => 'modal',
            'fields' => $this->getFields(),
            'config' => [],
            'attr'   => $this->getAttributes()
        ];
    }

    /**
     * Modal
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
    }

    /**
     * string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
