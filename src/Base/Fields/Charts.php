<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Element;

class Charts extends Element
{
    /**
     * @var null
     */
    private $chart = null;

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $class = '\Laradium\Laradium\Base\Charts\\' . ucfirst($name);
        if (class_exists($class)) {
            $this->chart = new $class($arguments);
        }

        return $this->chart;
    }

    /**
     * @return $this
     */
    public function build(): Element
    {
        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'type'   => 'charts',
            'chart'  => $this->chart->getChart() ?? '',
            'config' => [
                'col' => 'col-md-12',
            ],
            'attr'   => $this->getAttributes()
        ];
    }

}