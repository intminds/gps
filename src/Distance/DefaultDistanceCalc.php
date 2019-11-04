<?php
declare(strict_types=1);

namespace Intminds\GPS\Distance;

use Intminds\GPS\Defaults;
use Intminds\GPS\Movement\MovementCalcInterface;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

class DefaultDistanceCalc implements DistanceCalcInterface
{
    /**
     * @var MovementCalcInterface
     */
    protected $movementCalc;

    public function __construct(MovementCalcInterface $movementCalc = null)
    {
        $this->movementCalc = $movementCalc ?: Defaults::getMovementCalc();
    }

    public function calcPointsDistance(Points $points): float
    {
        $distance = 0;
        $count = sizeof($points);
        for ($i = 0; $i < $count - 1; ++$i) {
            $distance += $this->movementCalc->getDistance($points[$i + 1], $points[$i]);
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