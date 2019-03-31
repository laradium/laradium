<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class MapPosition extends Field
{
    /**
     * @var bool
     */
    protected $zoom = [
        'enabled' => false,
        'name'    => ''
    ];

    /**
     * @param $longitude
     */
    public function longitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @param $latitude
     */
    public function latitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return $this
     */
    public function zoomable($field): self
    {
        $this->zoom = [
            'enabled' => true,
            'name'    => $field
        ];

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        $data = parent::formattedResponse();
        $attributes = $this->getAttributes();
        array_pop($attributes);

        $this->zoom['value'] = $this->getModel()->{$this->zoom['name']} ?? 1;
        $data = [
            'zoom' => $this->zoom,

            'lat' => [
                'name'  => $this->getNameAttribute(array_merge($attributes, ['latitude'])),
                'value' => $this->getModel()->{$this->latitude} ?? '',
            ],
            'lng' => [
                'name'  => $this->getNameAttribute(array_merge($attributes, ['longitude'])),
                'value' => $this->getModel()->{$this->longitude} ?? '',
            ]
        ];

        return array_merge(parent::formattedResponse(), $data);
    }

}