<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Borders\BordersCalcInterface;
use Intminds\GPS\Borders\DefaultBordersCalc;
use Intminds\GPS\Distance\DefaultDistanceCalc;
use Intminds\GPS\Distance\DistanceCalcInterface;
use Intminds\GPS\Elevation\ElementaryElevationCalc;
use Intminds\GPS\Elevation\ElevationCalcInterface;
use Intminds\GPS\Flatten\DefaultFlattenCalc;
use Intminds\GPS\Flatten\FlattenCalcInterface;
use Intminds\GPS\Movement\MovementCalcInterface;
use Intminds\GPS\Movement\RoundEarthMovementCalc;

class Defaults
{
    public static function getMovementCalc(): MovementCalcInterface
    {
        return new RoundEarthMovementCalc();
    }

    public static function getDistanceCalc(): DistanceCalcInterface
    {
        return new DefaultDistanceCalc();
    }

    public static function getElevationCalc(): ElevationCalcInterface
    {
        return new ElementaryElevationCalc();
    }

    public static function getBordersCalc(): BordersCalcInterface
    {
        return new DefaultBordersCalc();
    }

    public static function getFlattenCalc(): FlattenCalcInterface
    {
        return new DefaultFlattenCalc();
    }
}