<?php
declare(strict_types=1);

namespace Intminds\GPS\Movement;

use Intminds\GPS\Point;

class RoundEarthMovementCalc implements MovementCalcInterface
{
    const EARTH_RADIUS = 6372795.477598;

    public function getDistance(Point $from, Point $to): float
    {
        $latFrom = deg2rad($from->lat);
        $lngFrom = deg2rad($from->lng);
        $latTo = deg2rad($to->lat);
        $lngTo = deg2rad($to->lng);

        $lonDelta = $lngTo - $lngFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2)
            + pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        $angle = atan2(sqrt($a), $b);
        return $angle * self::EARTH_RADIUS;
    }
}