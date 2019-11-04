<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Points;
use Intminds\GPS\Processors\MissingPropException;
use Intminds\GPS\Track;

class PropDistanceCalc implements DistanceCalcInterface
{
    public function calcPointsDistance(Points $points): float
    {
        if (!$points->allPointsHaveProp("distance")) {
            throw new MissingPropException("All points must have a 'distance' property when using PropDistanceCalc. Use DistanceProcessor first.");
        }
        if (is_null($points->getFinish())) {
            return 0.0;
        }
        return $points->getFinish()["distance"] - $points->getStart()["distance"];
    }

    public function calcTrackDistance(Track $track): float
    {
        if (!isset($track->getFinish()["distance"])) {
            throw new MissingPropException("All points must have a 'distance' property when using PropDistanceCalc. Use DistanceProcessor first.");
        }
        return $track->getFinish()["distance"];
    }
}