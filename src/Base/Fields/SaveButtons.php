<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class SaveButtons extends Element
{
    /**
     * @var bool
     */
    private $locale_selector = true;

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
                'locale_selector' => $this->locale_selector
            ],
            'attr'   => $this->getAttributes(),
        ];
    }

    /**
     * @return $this
     */
    public function withoutLanguageSelect(): self
    {
        $this->locale_selector = false;

        return $this;
    }
}
