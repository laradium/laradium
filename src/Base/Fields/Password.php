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
        $attributes = array_merge(['password']);
        parent::build($attributes);
//
//        $attributes = $this->getAttributes();
//        unset($attributes[count($attributes) - 1]);


//        $this->validationKey($attributes);
    }

    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();

        $attributes = $this->getAttributes();
        unset($attributes[count($attributes)-1]);
        $fields = [
            (new Hidden('field_name', $this->getModel()))
                ->build(array_merge($attributes, []))
                ->value($this->getFieldName())
                ->formattedResponse(),
            (new Hidden('crud_worker', $this->getModel()))
                ->build(array_merge($attributes, []))
                ->value(get_class($this))
                ->formattedResponse()
        ];
        $data['additional_fields'] = $fields;

        return $data;
    }
}