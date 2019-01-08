<?php

namespace Laradium\Laradium\Base\Fields;


use Illuminate\Database\Eloquent\Model;

class Row extends Col
{
    /**
     * @var mixed
     */
    private $use_block;

    /**
     * Row constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->use_block = array_first($parameters, null, false);

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
                'use_block' => $this->use_block,
                'col'       => 'col-md-12'
            ]
        ];
    }
}