<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Point;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

class RoundEarthDistanceCalc implements DistanceCalcInterface
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

    public function calcPointsDistance(Points $points): float
    {
        $distance = 0;
        $count = sizeof($points);
        for ($i = 0; $i < $count - 1; ++$i) {
            $distance += $this->getDistance($points[$i + 1], $points[$i]);
        }
        return $distance;
    }

    public function calcTrackDistance(Track $track): float
    {
        $distance = 0;
        foreach ($track->getSegments() as $segment) {
            $distance += $this->calcPointsDistance($segment->getPoints());
        }
        return $distance;
    }
}