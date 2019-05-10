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
     * @var string
     */
    private $height = '600px';

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
     * @return mixed
     */
    public function render()
    {
        return view('laradium::chart.index', [
            'type'    => $this->getType(),
            'chart'   => $this,
            'data'    => $this->getData(),
            'options' => $this->getOptions(),
            'height'  => $this->getHeight()
        ])->render();
    }

    /**
     * @param array|string $dataSet
     * @param array $dataSetOptions
     * @return $this
     */
    public function data($dataSet, $dataSetOptions = [])
    {
        $this->data = $dataSet;
        $this->dataSetOptions = $dataSetOptions;

        return $this;
    }

    /**
     * @param $options
     * @return $this
     */
    public function options($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param $labels
     * @return $this
     */
    public function labels($labels)
    {
        $this->labels = $labels;

        return $this;
    }

    /**
     * @param $height
     * @return $this
     */
    public function height($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $dataSet = $this->data;

        return [
            'labels'   => $this->getLabels($dataSet),
            'datasets' => $this->getDataSet($dataSet)
        ];
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return !empty($this->options) ? array_merge($this->options, $this->defaultOptions) : $this->defaultOptions;
    }

    /**
     * @param $dataSet
     * @return array
     */
    private function getDataSet($dataSet)
    {
        if ($this->isMultidimensional($dataSet)) {
            foreach ($dataSet as $key => $array) {
                $data[] = array_merge([
                    'data' => array_values($array)
                ], $this->dataSetOptions[$key] ?? []);
            }

            return $data;
        }

        return [array_merge([
            'data' => array_values($dataSet)
        ], $this->dataSetOptions)];
    }

    /**
     * @param $dataSet
     * @return array
     */
    private function getLabels($dataSet)
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
    private function isMultidimensional(array $array)
    {
        return count($array) !== count($array, COUNT_RECURSIVE);
    }
}