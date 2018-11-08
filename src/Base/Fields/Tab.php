<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\FieldSet;

class Tab
{

    /**
     * @var
     */
    private $fieldSet;
    private $name;
    private $closure;
    private $isTranslatable = false;

    public function __construct($name)
    {
        $this->name = array_first($name);
        $this->fieldSet = new FieldSet;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function build(Model $model)
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->model($model);
        $closure = $this->closure;
        $closure($fieldSet);

        return $this;
    }


    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure)
    {
        $this->closure = $closure;

        return $this;
    }

    public function formattedResponse()
    {
        $fields = [];
        foreach ($this->fieldSet->fields() as $field) {
            $field->build();

            if ($field->isTranslatable()) {
                $this->isTranslatable = true;
            }

            $fields[] = $field->formattedResponse();
        }

        return [
            'name'   => $this->name,
            'slug'   => str_slug($this->name, '_'),
            'type'   => 'tab',
            'fields' => $fields,
            'config' => [
                'is_translatable' => $this->isTranslatable,
                'col'             => 'col-md-12',
            ]
        ];
    }

    /**
     * @return bool
     */
    public function isTranslatable()
    {
        return $this->isTranslatable;
    }
}