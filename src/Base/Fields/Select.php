<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Select extends Field
{

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['options'] = collect($this->getOptions())->map(function ($text, $value) {
            return [
                'value'    => $value,
                'text'     => $text,
                'selected' => $this->getValue() == $value,
            ];
        })->toArray();

        return $data;
    }
}