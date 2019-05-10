<?php

namespace Laradium\Laradium\Base\Charts;

use Laradium\Laradium\Base\Chart;

class PolarArea extends Chart
{
    /**
     * @var string
     */
    protected $type = 'polarArea';

    /**
     * @var array
     */
    protected $defaultOptions = [
        'responsive'          => true,
        'maintainAspectRatio' => false,
        'animation'           => [
            'animateScale'  => true,
            'animateRotate' => false
        ],
        'scale'               => [
            'ticks'   => [
                'beginAtZero' => true
            ],
            'reverse' => false
        ]
    ];
}