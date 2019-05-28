<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Number extends Field
{

    /**
     * @var int|float
     */
    private $min;

    /**
     * @var int|float
     */
    private $max;

    /**
     * @var int|float
     */
    private $step;

    /**
     * @param $value
     * @return $this
     */
    public function min($value): self
    {
        $this->min = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function max($value): self
    {
        $this->max = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function step($value): self
    {
        $this->step = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        $data = parent::formattedResponse();
        $data['config']['step'] = $this->step;
        $data['config']['min'] = $this->min;
        $data['config']['max'] = $this->max;

        return $data;
    }
}