<?php

namespace Laradium\Laradium\Base;

class Chart
{
    /**
     * @var string
     */
    protected $type = '';

    /**
     * @var array
     */
    private $dataSet = [];

    /**
     * @var array
     */
    private $dataSetOptions = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array
     */
    private $labels = [];

    /**
     * @var array
     */
    protected $defaultOptions = [
        'responsive'          => true,
        'maintainAspectRatio' => false,
        'scales'              => [
            'yAxes' => [
                [
                    'ticks' => [
                        'beginAtZero' => true
                    ]
                ]
            ],
            'xAxes' => [
                [
                    'ticks' => [
                        'beginAtZero' => true
                    ]
                ]
            ],
        ]
    ];

    /**
     * Chart constructor.
     * @param array $dataSetOptions
     */
    public function __construct($dataSetOptions = [])
    {
        $this->dataSetOptions = $this->getType() !== 'pie' ? array_collapse($dataSetOptions) : $dataSetOptions;
    }

    /**
     * @return array
     */
    public function getChart(): array
    {
        return [
            'type'    => $this->getType(),
            'chart'   => $this,
            'data'    => $this->getData(),
            'options' => $this->getOptions()
        ];
    }

    /**
     * @param array|string $dataSet
     * @return $this
     */
    public function data($dataSet): self
    {
        $this->data = $dataSet;

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function options($options = []): self
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param $labels
     * @return $this
     */
    public function labels($labels = []): self
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getDataSource(): string
    {
        return is_array($this->data) ? 'array' : 'ajax';
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        $dataSet = array_values($this->data);

        if ($this->getDataSource() === 'ajax') {
            return [
                'source' => 'ajax',
                'url'    => $dataSet
            ];
        }

        return [
            'source'   => 'array',
            'labels'   => $this->getLabels($dataSet),
            'datasets' => $this->getDataSet($dataSet)
        ];
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return !empty($this->options) ? array_merge($this->options, $this->defaultOptions) : $this->defaultOptions;
    }

    /**
     * @param $dataSet
     * @return array
     */
    private function getDataSet($dataSet): array
    {
        if ($this->isMultidimensional($dataSet)) {
            $data = [];

            foreach ($dataSet as $key => $array) {
                $data[] = array_merge([
                    'data' => array_values($array)
                ], $this->dataSetOptions[$key] ?? []);
            }

            return $data;
        }

        return [array_merge([
            'data' => array_values($dataSet)
        ], array_collapse($this->dataSetOptions))];
    }

    /**
     * @param $dataSet
     * @return array
     */
    private function getLabels($dataSet): array
    {
        if (!empty($this->labels)) {
            return $this->labels;
        }

        if ($this->isMultidimensional($dataSet)) {
            return array_keys($dataSet[0]);
        }

        return array_keys($dataSet);
    }

    /**
     * @param array $array
     * @return bool
     */
    private function isMultidimensional(array $array): bool
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }
}
