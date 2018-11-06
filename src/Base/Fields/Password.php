<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Password extends Field
{
    public function formattedResponse(): array
    {
        $field = new Field(['password'], $this->getModel());
        $attributes = $this->getAttributes();
        unset($attributes[count($attributes) - 1]);
        $data = $field
            ->build($attributes)
            ->formattedResponse();

        $fields = [];

        $fields[] = (new Hidden('field_name', $field->getModel()))
            ->build(array_merge($field->getAttributes(), []))
            ->value($this->getFieldName())
            ->formattedResponse();

        dd($fields);

        $data['fields'] = $fields;

        return $data;
    }
}