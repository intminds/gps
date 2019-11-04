<?php
declare(strict_types=1);

namespace Intminds\GPS\Processors;

use Intminds\GPS\Defaults;
use Intminds\GPS\Distance\DistanceCalcInterface;
use Intminds\GPS\Points;
use Intminds\GPS\Track;

class DistanceProcessor implements ProcessorInterface
{
    /**
     * @var DistanceCalcInterface
     */
    protected $distanceCalc;

    public function __construct(DistanceCalcInterface $distanceCalc = null)
    {
        $this->distanceCalc = $distanceCalc ?: Defaults::getDistanceCalc();
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
            $distance += $this->distanceCalc->getDistance($points[$i], $points[$i - 1]);
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