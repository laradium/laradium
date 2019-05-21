<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Code extends Field
{

    /**
     * @var string
     */
    private $style = 'html';

    /**
     * @param string $value
     * @return Field
     */
    public function style(string $value): Field
    {
        $this->style = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getStyle(): string
    {
        return $this->style;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        $data = parent::formattedResponse();
        $data['style'] = $this->getStyle();

        return $data;
    }
}