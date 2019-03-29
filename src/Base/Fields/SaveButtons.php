<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class SaveButtons extends Element
{
    /**
     * @var bool
     */
    private $languageSelect = true;

    /**
     * SaveButtons constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $closure = array_first($parameters, null, false);

        if ($closure) {
            $this->fields($closure);
        }

        parent::__construct([12, 'md'], $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => 'save-buttons',
            'slug'   => 'save-buttons',
            'type'   => 'save-buttons',
            'fields' => $this->getFields(),
            'config' => [
                'language_select' => $this->languageSelect
            ],
            'attr'   => $this->getAttributes(),
        ];
    }

    /**
     * @return $this
     */
    public function withoutLanguageSelect(): self
    {
        $this->languageSelect = false;

        return $this;
    }
}
