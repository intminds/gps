<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Defaults;
use Intminds\GPS\Movement\MovementCalcInterface;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

class DistanceProcessor implements ProcessorInterface
{
    /**
     * @var MovementCalcInterface
     */
    protected $movementCalc;

    public function __construct(MovementCalcInterface $movementCalc = null)
    {
        $this->movementCalc = $movementCalc ?: Defaults::getMovementCalc();
    }

    protected function applyToPoints(Points $points, float $startDistance): void
    {
        $count = sizeof($points);
        if (isset($points[0])) {
            $points[0]["distance"] = $startDistance;
        }
        if ($count < 2) {
            return;
        }
        $distance = $startDistance;
        for ($i = 1; $i < $count; ++$i) {
            $distance += $this->movementCalc->getDistance($points[$i], $points[$i - 1]);
            $points[$i]["distance"] = $distance;
        }
    }

    public function applyToTrack(Track $track): void
    {
        $distance = 0.0;
        foreach ($track->getSegments() as $segment) {
            $this->applyToPoints($segment->getPoints(), $distance);
            $distance = $segment->getFinish()["distance"];
        }
    }
}