<?php

namespace Netcore\Aven\Aven\Fields;


use Netcore\Aven\Aven\Field;

class Textarea extends Field
{

    /**
     * @var string
     */
    public $field = 'textarea';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function render()
    {
        $field = $this;
        return view('admin.fields.' . $this->field, compact('field'));
    }
}