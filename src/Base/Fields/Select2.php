<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;

class Select2 extends Field
{
    /**
     * @var string|null
     */
    protected $source;

    /**
     * @var string
     */
    protected $dataProperty = 'data';

    /**
     * @var string
     */
    protected $searchParam = 'query';

    /**
     * @var array
     */
    protected $queryParams = [];

    /**
     * @var string
     */
    protected $idField = 'id';

    /**
     * @var string
     */
    protected $textField = 'text';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param $value
     * @return $this
     */
    public function source($value): self
    {
        $this->source = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function dataProperty($value): self
    {
        $this->dataProperty = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function searchParam($value): self
    {
        $this->searchParam = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function queryParams($value): self
    {
        $this->queryParams = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function idField($value): self
    {
        $this->idField = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function textField($value): self
    {
        $this->textField = $value;

        return $this;
    }

    /**
     * @param array $options
     * @return $this
     */
    public function options(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['options'] = $this->getOptions();
        $data['config']['source'] = $this->source;
        $data['config']['data_property'] = $this->dataProperty;
        $data['config']['search_param'] = $this->searchParam;
        $data['config']['query_params'] = $this->queryParams;
        $data['config']['id_field'] = $this->idField;
        $data['config']['text_field'] = $this->textField;

        return $data;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        $options = [];

        foreach ($this->options as $key => $value) {
            $options[] = [
                'id'       => $key,
                'text'     => $value,
                'selected' => $key == $this->getValue()
            ];
        }

        return $options;
    }
}
