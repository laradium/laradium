<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;

class Hidden extends Field
{
    /**
     * Hidden constructor.
     * @param $fieldName
     * @param Model $model
     */
    public function __construct($fieldName, Model $model)
    {
        parent::__construct(is_array($fieldName) ? $fieldName : [$fieldName], $model);
    }
}