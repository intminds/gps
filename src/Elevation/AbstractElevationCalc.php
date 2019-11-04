<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\ElevationTotal;
use Intminds\GPS\Track;

abstract class AbstractElevationCalc implements ElevationCalcInterface
{
    public function calcTrackElevation(Track $track): ElevationTotal
    {
        $result = new ElevationTotal();
        foreach ($track->getSegments() as $segment) {
            $segmentElevation = $this->calcPointsElevation($segment->getPoints());
            $result->elevationGain += $segmentElevation->elevationGain;
            $result->elevationLoss += $segmentElevation->elevationLoss;
        }
        return $result;
    }
}