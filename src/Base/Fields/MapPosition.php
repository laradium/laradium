<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class MapPosition extends Field
{
    /**
     * @var string
     */
    protected $longitude = '';

    /**
     * @var string
     */
    protected $latitude = '';

    /**
     * @var array
     */
    protected $zoom = [
        'enabled' => false,
        'name'    => ''
    ];

    /**
     * @param $longitude
     * @return $this
     */
    public function longitude($longitude)
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @param $latitude
     * @return $this
     */
    public function latitude($latitude)
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @param $field
     * @return $this
     */
    public function zoomable($field)
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