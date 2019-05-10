<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Base\Charts\Bar;
use Laradium\Laradium\Base\Charts\Bubble;
use Laradium\Laradium\Base\Charts\Doughnut;
use Laradium\Laradium\Base\Charts\Line;
use Laradium\Laradium\Base\Charts\Pie;
use Laradium\Laradium\Base\Charts\PolarArea;
use Laradium\Laradium\Base\Charts\Radar;
use Laradium\Laradium\Base\Charts\Scatter;

class Charts
{
    /**
     * @return Line
     */
    public function line()
    {
        return new Line();
    }

    /**
     * @return Bar
     */
    public function bar()
    {
        return new Bar();
    }

    /**
     * @return Pie
     */
    public function pie()
    {
        return new Pie();
    }

    /**
     * @return Scatter
     */
    public function scatter()
    {
        return new Scatter();
    }

    /**
     * @return Doughnut
     */
    public function doughnut()
    {
        return new Doughnut();
    }

    /**
     * @return Bubble
     */
    public function bubble()
    {
        return new Bubble();
    }

    /**
     * @return Radar
     */
    public function radar()
    {
        return new Radar();
    }

    /**
     * @return PolarArea
     */
    public function polarArea()
    {
        return new PolarArea();
    }
}