<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class LanguageSelect extends Element
{
    /**
     * LanguageSelect constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->setName('language-selector');

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getName(),
            'slug'   => str_slug($this->getName(), '_'),
            'type'   => 'language-selector',
            'fields' => $this->getFields(),
            'config' => []
        ];
    }
}