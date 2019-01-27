<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Wysiwyg extends Field
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @param array $config
     * @return $this
     */
    public function config($config = [])
    {
        $this->config = $config;

        return $this;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['config'] = array_merge($data['config'], $this->getConfig());
        return $data;
    }
}