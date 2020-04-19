<?php

namespace Laradium\Laradium\Base\Fields;

use Carbon\Carbon;
use Laradium\Laradium\Base\Field;

class Date extends Field
{
    /**
     * @var array
     */
    protected $config = [
        'format'         => 'Y-m-d',
        'dayOfWeekStart' => 1
    ];

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        parent::build(array_merge($attributes, ['date']));

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();

        $attributes = $this->getAttributes();
        unset($attributes[count($attributes) - 1]);

        $data['worker'] = (new Hidden('crud_worker', $this->getModel()))
            ->build($attributes)
            ->value(get_class($this))
            ->replacementAttributes($this->getReplacementAttributes())
            ->formattedResponse();
        $data['value'] = $this->getValue() ? Carbon::parse($this->getValue())->format($this->config['format']) : '';
        $data['config'] = array_merge($data['config'], $this->getConfig());

        return $data;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function config(array $config)
    {
        $this->config = array_merge($this->config, $config);

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
