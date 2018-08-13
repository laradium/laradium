<?php

namespace Netcore\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Select extends Field
{

    /**
     * @var string
     */
    public $field = 'select';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $field = $this;
        $options = array_pop($field->parameters);
        return view('admin.fields.' . $this->field, compact('field', 'options'));
    }
}