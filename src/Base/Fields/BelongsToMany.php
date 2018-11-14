<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;

class BelongsToMany extends Field
{

    /**
     * @var string
     */
    private $relationName;

    /**
     * @var string
     */
    private $title = 'name';

    /**
     * @var
     */
    private $items;

    /**
     * @var array
     */
    protected $fieldCol = [
        'size' => 2,
        'type' => 'md'
    ];

    /**
     * BelongsToMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
    }

    /**
     * @param array $attributes
     * @return Field
     */
    public function build($attributes = [])
    {
        parent::build();

        $model = $this->getModel();
        $relationModel = $model->{$this->relationName}()->getModel();

        $this->items = $relationModel->all()->map(function ($item) use($model) {
            $isChecked = false;
            if($checkedCategory = $model->{$this->relationName}->where('id', $item->id)->first()) {
                $isChecked = true;
            }
            return [
                'id' => $item->id,
                'name' => $item->{$this->title},
                'is_checked' => $isChecked,
            ];
        });

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['value'] = get_class($this);
        $data['items'] = $this->items;
        $data['config']['field_col'] = $this->fieldCol;

        return $data;
    }

    /**
     * @param $value
     * @return $this
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
    }


    /**
     * @param int $size
     * @param string $type
     * @return $this
     */
    public function fieldCol($size = 2, $type = 'md')
    {
        $this->fieldCol = compact('size', 'type');
        return $this;
    }
}