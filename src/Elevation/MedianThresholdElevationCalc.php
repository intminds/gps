<?php
declare(strict_types=1);

namespace Intminds\GPS\Elevation;

use Intminds\GPS\ElevationTotal;
use Intminds\GPS\Points;

class MedianThresholdElevationCalc extends AbstractElevationCalc
{
    /**
     * @var float
     */
    protected $minimalChange;
    /**
     * @var int
     */
    protected $repeatCount;

    public function __construct(float $minimalChange = 2.0, $repeatCount = 9)
    {
        $this->minimalChange = $minimalChange;
        $this->repeatCount = $repeatCount;
    }

    public function calcPointsElevation(Points $points): ElevationTotal
    {
        $result = new ElevationTotal();
        $count = sizeof($points);
        if ($count >= 2) {
            $thresholdCalc = new ThresholdElevationCalc($this->minimalChange);
            $gains = [];
            $losses = [];
            for ($iter = 0; $iter < $this->repeatCount; ++$iter) {
                $elevationTotal = $thresholdCalc->calcPointsElevationWithOffset($points, mt_rand(0, $count - 1));
                $gains[$iter] = $elevationTotal->elevationGain;
                $losses[$iter] = $elevationTotal->elevationLoss;
            }
            sort($gains);
            sort($losses);
            $result->elevationGain = $gains[(int)floor(sizeof($gains) / 2)];
            $result->elevationLoss = $losses[(int)floor(sizeof($losses) / 2)];
        }
        return $result;
    }
}