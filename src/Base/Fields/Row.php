<?php

namespace Laradium\Laradium\Base\Fields;


use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class Row extends Element
{
    /**
     * @var boolean
     */
    private $useBlock = false;

    /**
     * Row constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $closure = array_first($parameters, null, true);

        if ($closure) {
            $this->fields($closure);
        }

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => 'row',
            'slug'   => 'row',
            'type'   => 'row',
            'fields' => $this->getFields(),
            'config' => [
                'use_block' => $this->useBlock,
                'col'       => 'col-md-12'
            ],
            'attr'   => $this->getAttributes()
        ];
    }

    /**
     * @return $this
     */
    public function block(): self
    {
        $this->useBlock = true;

        return $this;
    }
}
