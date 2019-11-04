<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\ElevationTotal;
use Intminds\GPS\Points;

class HysteresisElevationCalc extends AbstractElevationCalc
{
    /**
     * @var float
     */
    protected $minimalChange;

    public function __construct(float $minimalChange = 2.0)
    {
        $this->minimalChange = $minimalChange;
    }

    public function calcPointsElevation(Points $points): ElevationTotal
    {
        return $this->calcPointsElevationWithOffset($points);
    }

    public function calcPointsElevationWithOffset(Points $points, $startOffset = 0): ElevationTotal
    {
        $result = new ElevationTotal();
        $count = sizeof($points);
        if ($count < 2) {
            return $result;
        }
        if ($startOffset >= $count) {
            throw new \OutOfBoundsException("\$startOffset must be less than sizeof(\$points)");
        }
        $lastUsedPoint = $points[$startOffset];
        for ($i = $startOffset; $i < $count; ++$i) {
            $gain = $points[$i]->alt - $lastUsedPoint->alt;
            if (abs($gain) >= $this->minimalChange) {
                if ($gain >= 0) {
                    $result->elevationGain += $gain;
                } else {
                    $result->elevationLoss += (-$gain);
                }
                $lastUsedPoint = $points[$i];
            }
        }
        $gain = $points[$count - 1]->alt - $lastUsedPoint->alt;
        if ($gain >= 0) {
            $result->elevationGain += $gain;
        } else {
            $result->elevationLoss += (-$gain);
        }
        $lastUsedPoint = $points[$startOffset];
        for ($i = $startOffset; $i >= 0; --$i) {
            $gain = $lastUsedPoint->alt - $points[$i]->alt;
            if (abs($gain) >= $this->minimalChange) {
                if ($gain >= 0) {
                    $result->elevationGain += $gain;
                } else {
                    $result->elevationLoss += (-$gain);
                }
                $lastUsedPoint = $points[$i];
            }
        }
        $gain = $lastUsedPoint->alt - $points[0]->alt;
        if ($gain >= 0) {
            $result->elevationGain += $gain;
        } else {
            $result->elevationLoss += (-$gain);
        }
        return $result;
    }
}