<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;

class Breadcrumbs extends Element
{

    /**
     * @var array
     */
    private $breadcrumbs;

    /**
     * Block constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, $model)
    {
        $this->breadcrumbs = array_first($parameters);
        $this->setName('Breadcrumbs');

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function getBreadcrumbs(): array
    {
        return $this->breadcrumbs;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getName(),
            'type'   => 'breadcrumbs',
            'breadcrumbs' => $this->getBreadcrumbs(),
            'config' => [
                'col' => 'col-md-12',
            ],
            'attr'   => $this->getAttributes()
        ];
    }
}
