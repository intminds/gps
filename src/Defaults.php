<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Distance\DistanceCalcInterface;
use Intminds\GPS\Distance\RoundEarthDistanceCalc;

class Defaults
{
    public static function getDistanceCalc(): DistanceCalcInterface
    {
        return new RoundEarthDistanceCalc();
    }
}