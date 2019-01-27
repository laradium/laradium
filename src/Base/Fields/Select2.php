<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Select2 extends Field
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
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['options'] = $this->getOptions();

        return $data;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = [];

        foreach ($this->options as $key => $value) {
            $options[] = [
                'id'       => $key,
                'text'     => $value,
                'selected' => $key == $this->getValue()
            ];
        }

        return $options;
    }
}