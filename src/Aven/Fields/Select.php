<?php

namespace Netcore\Aven\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Select extends Field
{

    /**
     * @var string
     */
    protected $view = 'aven::admin.fields.select';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}