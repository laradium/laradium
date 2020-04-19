<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Password extends Field
{

    /**
     * @param array $attributes
     * @return Field|void
     */
    public function build($attributes = [])
    {
        parent::build(array_merge($attributes, ['password']));

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();

        $attributes = $this->getAttributes();
        unset($attributes[count($attributes)-1]);

        $data['worker'] = (new Hidden('crud_worker', $this->getModel()))
            ->build(array_merge($attributes, []))
            ->value(get_class($this))
            ->formattedResponse();

        return $data;
    }
}