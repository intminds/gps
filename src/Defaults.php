<?php
declare(strict_types=1);

namespace Intminds\GPS;

use Intminds\GPS\Movement\MovementCalcInterface;
use Intminds\GPS\Movement\RoundEarthMovementCalc;

class Defaults
{
    public static function getMovementCalc(): MovementCalcInterface
    {
        return new RoundEarthMovementCalc();
    }
}