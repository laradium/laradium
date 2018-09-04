<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;

class BelongsToMany extends Field
{

    /**
     * @var
     */
    protected $relationModel;

    /**
     * @var string
     */
    protected $relationName;

    /**
     * BelongsToMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = $this->name;

        if (count($parameters) > 1) {
            $this->label = array_last($parameters);
        }
    }

    /**
     * @param null $field
     * @return array
     */
    public function formatedResponse($field = null)
    {
        $field = !is_null($field) ? $field : $this;

        $relatedItems = $this->relation()->get()->pluck('id')->toArray();
        $relationModel = $this->relation()->getModel();
        $items = new $relationModel;
        $items = $items->get();

        $attributes = collect($field->getNameAttributeList())->map(function ($item, $index) {
            if ($item == '__ID__') {
                return '__ID' . ($index + 1) . '__';
            } else {
                return $item;
            }
        });

        $field->setNameAttributeList($attributes->toArray());

        $attributes = $attributes->filter(function ($item) {
            return str_contains($item, '__ID');
        });

        return [
            'type'                   => 'belongs-to-many',
            'name'                   => $field->getNameAttribute(),
            'replacementAttributes'  => $attributes->toArray(),
            'label'                  => $this->label ?: $this->name,
            'items'                  => $items->map(function ($item) use ($relatedItems) {
                return [
                    'id'      => $item->id,
                    'name'    => $item->name,
                    'checked' => in_array($item->id, $relatedItems),
                ];
            })->toArray(),
        ];
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function relation(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->model()->load($this->relationName)->{$this->relationName}();
    }
}