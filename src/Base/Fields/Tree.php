<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;

class Tree extends HasMany
{
    /**
     * HasMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->nestable();
        $this->sortable();
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $tree = [];
        foreach($data['entries'] as $entry) {
            $tree[] = $entry['tree'];
        }
        $data['tree'] = $tree;
        $data['value'] = HasMany::class;

        return $data;
    }
}