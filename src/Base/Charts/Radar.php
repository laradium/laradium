<?php

namespace Laradium\Laradium\Base\Charts;

use Laradium\Laradium\Base\Chart;

class Radar extends Chart
{
    /**
     * @var string
     */
    protected $type = 'radar';

    /**
     * @var array
     */
    protected $defaultOptions = [
        'responsive'          => true,
        'maintainAspectRatio' => false,
        'scale'               => [
            'ticks' => [
                'beginAtZero' => true
            ],
        ]
    ];
}