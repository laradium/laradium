<?php

namespace Laradium\Laradium\Base\Charts;

use Laradium\Laradium\Base\Chart;

class Doughnut extends Chart
{
    /**
     * @var string
     */
    protected $type = 'doughnut';

    /**
     * @var array
     */
    protected $defaultOptions = [
        'responsive'          => true,
        'maintainAspectRatio' => false,
        'animation'           => [
            'animateScale'  => true,
            'animateRotate' => true
        ]
    ];
}