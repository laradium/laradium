<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;

class Hidden extends Field
{
    public function __construct($fieldName, Model $model)
    {
        parent::__construct([$fieldName], $model);
    }
}