<?php

namespace Laradium\Laradium\Base\Fields;

class Block extends Col
{
    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getName(),
            'slug'   => str_slug($this->getName(), '_'),
            'type'   => 'block',
            'fields' => $this->getFields(),
            'config' => [
                'col' => $this->getName(),
            ]
        ];
    }
}