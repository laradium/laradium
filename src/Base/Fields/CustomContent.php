<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class CustomContent extends Element
{
    /**
     * @var string
     */
    protected $content;

    /**
     * CustomContent constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        $this->setName('customContent');

        $content = array_get($parameters, 0, '');
        if (is_callable($content)) {
            $this->content = $content();
        } else {
            $this->content = $content;
        }

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'    => $this->getName(),
            'type'    => 'custom-content',
            'config'  => [
                'is_translatable' => $this->getIsTranslatable(),
            ],
            'content' => $this->content,
            'attr'    => $this->getAttributes()
        ];
    }
}
