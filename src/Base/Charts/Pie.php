<?php

namespace Laradium\Laradium\Base\Charts;

use Laradium\Laradium\Base\Chart;

class Pie extends Chart
{
    /**
     * @var string
     */
    protected $type = 'pie';

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