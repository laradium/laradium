<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class Block extends Element
{
    /**
     * Block constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->setName('col-' . array_get($parameters, 1, 'md') . '-' . array_get($parameters, 0, '12'));

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getName(),
            'type'   => 'block',
            'fields' => $this->getFields(),
            'config' => [
                'col' => $this->getName(),
            ],
            'attr'   => $this->getAttributes()
        ];
    }
}
