<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Boolean extends Field
{

    /**
     * @return array
     */
    public function formattedResponse()
    {
        return [
            'type'         => strtolower(array_last(explode('\\', get_class($this)))),
            'label'        => $this->getLabel(),
            'name'         => !$this->isTranslatable() ? $this->getNameAttribute() : null,
            'value'        => !$this->isTranslatable() ? $this->getValue() : null,
            'translations' => $this->getTranslations(),
            'checked'      => $this->getValue() == 1,
            'config'       => [
                'is_translatable' => $this->isTranslatable(),
                'col'             => $this->getCol(),
                'tab'             => $this->getTab(),
            ]
        ];
    }
}